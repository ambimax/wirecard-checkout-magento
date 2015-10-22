var wirecarcdCheckoutPageResponseData = [];

function toggleQMoreIFrame() {
    if ($('qmore-iframe-div')) {
        var viewportHeight = document.viewport.getHeight(),
            docHeight = $$('body')[0].getHeight(),
            height = docHeight > viewportHeight ? docHeight : viewportHeight;
        $('qmore-iframe-div').toggle();
        $('window-overlay-qmore').setStyle({ height: height + 'px' }).toggle();
    }
}

Event.observe(window, 'load', function () {
    if (typeof WirecardCEE_DataStorage == 'function') {

        payment.save = payment.save.wrap(function (origSaveMethod) {

            if (this.currentMethod && this.currentMethod.substr(0, 22) == 'wirecard_checkout_page'
                && $(this.currentMethod + '_seamless')
                && $(this.currentMethod + '_new').value == '1'
                && (typeof wirecarcdCheckoutPageResponseData[this.currentMethod] === 'undefined' || wirecarcdCheckoutPageResponseData[this.currentMethod] === false)) {

                if (checkout.loadWaiting != false) return;
                var validator = new Validation(this.form);
                var valid = validator.validate();

                if (this.validate() && valid) {
                    checkout.setLoadWaiting('payment');
                    var application = new wirecardCheckoutPageApplication();
                    var data;

                    if (this.currentMethod.substr(23) == 'cc' || this.currentMethod.substr(23) == 'ccMoto') {
                        data = application.buildCcData();

                    } else if (this.currentMethod.substr(23) == 'elv') {
                        data = application.buildElvData();

                    } else if (this.currentMethod.substr(23) == 'sepadd') {
                        data = application.buildSepaDdData();

                    } else if (this.currentMethod.substr(23) == 'pbx') {
                        data = application.buildPbxData();

                    } else if (this.currentMethod.substr(23) == 'wgp') {
                        data = application.buildWgpData();

                    } else if (this.currentMethod.substr(23) == 'c2p') {
                        data = application.buildC2PData();
                    }

                    application.sendRequest(data);
                }

            } else {
                origSaveMethod();

            }
        });
    }

    Review.prototype.nextStep = Review.prototype.nextStep.wrap(function (next, transport) {
        outerTransport = transport;
        nextStep = next;
        var outerResponse = eval('(' + outerTransport.responseText + ')');
        if (typeof outerResponse.redirect == 'undefined') {
            nextStep(outerTransport);
        }
        else {
            var params = {'paymentMethod': payment.currentMethod};
            var request = new Ajax.Request(
                qmoreIsIframe,
                {
                    method: 'get',
                    parameters: params,
                    onSuccess: function (innerTransport) {
                        if (innerTransport && innerTransport.responseText) {
                            try {
                                var innerResponse = eval('(' + innerTransport.responseText + ')');
                                var outerResponse = eval('(' + outerTransport.responseText + ')');
                            }
                            catch (e) {
                                innerResponse = {};
                            }
                            if (innerResponse.isIframe) {
                                //show iframe and set link
                                toggleQMoreIFrame();
                                $('qmore-iframe').src = outerResponse.redirect;
                            }
                            else {
                                nextStep(outerTransport);
                            }
                        }
                    },
                    onFailure: function (innerTransport) {
                        nextStep(outerTransport);
                    }
                });
        }
    });
});

var wirecardCheckoutPageApplication = Class.create();
wirecardCheckoutPageApplication.prototype = {

    initialize: function () {
    },

    buildCcData: function () {
        var data = {
            paymentType: 'CCARD'
        };

        if ($(payment.currentMethod + '_pan') === null)
            return data;

        data.pan = $(payment.currentMethod + '_pan').value;
        data.expirationMonth = $(payment.currentMethod + '_expirationMonth').value;
        data.expirationYear = $(payment.currentMethod + '_expirationYear').value;
        if ($(payment.currentMethod + '_cardholdername')) {
            data.cardholdername = $(payment.currentMethod + '_cardholdername').value;
        }
        if ($(payment.currentMethod + '_cardVerifyCode')) {
            data.cardVerifyCode=  $(payment.currentMethod + '_cardVerifyCode').value;
        }
        if ($(payment.currentMethod + '_issueMonth') && $(payment.currentMethod + '_issueYear')) {
            data.issueYear = $(payment.currentMethod + '_issueYear').value;
            data.issueMonth = $(payment.currentMethod + '_issueMonth').value;
        }
        if ($(payment.currentMethod + '_issueNumber')) {
            data.issueNumber = $(payment.currentMethod + '_issueNumber').value;
        }

        return data;
    },

    buildElvData: function () {
        var data = {
            paymentType: "ELV",
            accountOwner: $(payment.currentMethod + '_accountOwner').value,
            bankName: $(payment.currentMethod + '_bankName').value,
            bankCountry: $(payment.currentMethod + '_bankCountry').value,
            bankAccount: $(payment.currentMethod + '_bankAccount').value,
            bankNumber: $(payment.currentMethod + '_bankNumber').value
        }

        return data;
    },

    buildSepaDdData: function () {
        var data = {
            paymentType: "SEPA-DD",
            accountOwner: $(payment.currentMethod + '_accountOwner').value,
            bankBic: $(payment.currentMethod + '_bankBic').value,
            bankAccountIban: $(payment.currentMethod + '_bankAccountIban').value
        }

        return data;
    },


    buildPbxData: function () {
        var data = {
            paymentType: "PBX",
            payerPayboxNumber: $(payment.currentMethod + '_payerPayboxNumber').value
        }
        return data;
    },

    buildWgpData: function () {
        var data = {
            paymentType: "GIROPAY",
            accountOwner: $(payment.currentMethod + '_accountOwner').value,
            bankAccount: $(payment.currentMethod + '_bankAccount').value,
            bankNumber: $(payment.currentMethod + '_bankNumber').value
        }
        return data;
    },

    buildC2PData: function () {
        var data = {
            paymentType: "C2P",
            username: $(payment.currentMethod + '_username').value,
            pan: $(payment.currentMethod + '_pan').value
        }
        return data;
    },

    sendRequest: function (data) {

        var wirecardCee = new WirecardCEE_DataStorage;
        var request = wirecardCee.storePaymentInformation(data, function (response) {

            processResponse(response);

            if (response.getStatus() == 0) {

                new Ajax.Request(
                    wirecardCheckoutPageSaveSessInfo,
                    {
                        method: 'post',
                        parameters: Form.serialize(payment.form)
                    }
                );

                var request = new Ajax.Request(
                    payment.saveUrl,
                    {
                        method: 'post',
                        onComplete: payment.onComplete,
                        onSuccess: payment.onSave,
                        onFailure: checkout.ajaxFailure.bind(checkout),
                        parameters: Form.serialize(payment.form)
                    }
                );
            }
        });

        // no postMessage support, make read request to datastore to check for stored data
        if (request === null)
        {
            new Ajax.Request(
            wirecardCheckoutPageReadDatastorage,
            {
                method: 'post',
                parameters: Form.serialize(payment.form),
                onComplete: function (resp) {
                    response = window.JSON.parse(resp.responseText);

                    if (response.status == 1) {
                        new Ajax.Request(
                            payment.saveUrl,
                            {
                                method: 'post',
                                onComplete: payment.onComplete,
                                onSuccess: payment.onSave,
                                onFailure: checkout.ajaxFailure.bind(checkout),
                                parameters: Form.serialize(payment.form)
    }
                        );
                    } else {
                        alert('no stored paymentinformation found');
                        checkout.setLoadWaiting(false);
}
                }
            });
        }
    }
}

function processResponse(response) {

    if (response.getErrors()) {
        var errorMsg = '';
        var errors = response.response.error;
        for (var i = 0; i <= response.response.errors; i++) {
            if (typeof errors[i] === 'undefined') {
                continue;
            }
            errorMsg += errors[i].consumerMessage.strip() + "\n\r";
        }
        //we have to htmlentities decode this
        alert(html_entity_decode(errorMsg));
        checkout.setLoadWaiting(false);

    } else {

        if (payment.currentMethod.substr(23) == 'cc' || payment.currentMethod.substr(23) == 'ccMoto') {

            // fill in storageId
            $('payment_form_' + payment.currentMethod).select('input[type="hidden"]').each(function (el) {
                if (el.name == 'payment[additional_data]') {
                    el.value = response.response.storageId;
                    return false;
                }
            });
            if ($(payment.currentMethod + '_pan') === null)
                return;
        }

        prepareSubmittedFields(response.response);
        wirecarcdCheckoutPageResponseData[payment.currentMethod] = true;
    }
}

function html_entity_decode(str) {
    //jd-tech.net
    var tarea = document.createElement('textarea');
    tarea.innerHTML = str;
    return tarea.value;
    tarea.parentNode.removeChild(tarea);
}

function prepareSubmittedFields(response) {

    if (payment.currentMethod.substr(23) == 'cc' || payment.currentMethod.substr(23) == 'ccMoto') {

        enterAnonData(response.paymentInformation);
        $(payment.currentMethod + '_saved_data').show();
        $(payment.currentMethod + '_new_data').hide();
        emptyPaymentFields();
    } else {

        $$('#payment_form_' + payment.currentMethod + ' .no-submit').each(function (el) {
            el.observe('change', function (el) {
                $(payment.currentMethod + '_new').value = '1';
                wirecarcdCheckoutPageResponseData[payment.currentMethod] = false;
            });
        });
    }

    var elements = $('payment_form_' + payment.currentMethod).select('input[type="hidden"]').each(function (el) {
        switch (el.name) {
            case 'payment[cc_owner]':
            case 'payment[ccMoto_owner]':
                el.value = response.paymentInformation.cardholdername;
                break;
            case 'payment[cc_type]':
            case 'payment[ccMoto_type]':
                el.value = response.paymentInformation.brand;
                break;
            case 'payment[cc_number]':
            case 'payment[ccMoto_number]':
                el.value = response.paymentInformation.anonymousPan;
                break;
            case 'payment[cc_exp_month]':
            case 'payment[ccMoto_exp_month]':
                el.value = response.paymentInformation.expiry.substr(0, response.paymentInformation.expiry.lastIndexOf('/'));
                break;
            case 'payment[cc_exp_year]':
            case 'payment[ccMoto_exp_year]':
                el.value = response.paymentInformation.expiry.substr(response.paymentInformation.expiry.lastIndexOf('/') + 1);
                break;
            case 'payment[additional_data]':
                el.value = response.storageId;
                break;
            case payment.currentMethod + '_new':
                el.value = '0';
                break;
            default:
                break;
        }
    });
}

function changePaymentData() {

    new Ajax.Request(
        wirecardCheckoutPageDeleteSessInfo,
        {
            method: 'post',
            onSuccess: function () {
                emptyHiddenFields();
                wirecarcdCheckoutPageResponseData[payment.currentMethod] = false;
                $(payment.currentMethod + '_new').value = '1';
                $(payment.currentMethod + '_saved_data').hide();
                $(payment.currentMethod + '_new_data').show();
            }
        }
    );
}

function emptyPaymentFields() {
    var oldElements = $$('#payment_form_' + payment.currentMethod + ' .no-submit').each(function (el) {
        el.value = '';
    });
}

function emptyHiddenFields() {
    var elements = $('payment_form_' + payment.currentMethod).select('input[type="hidden"]').each(function (el) {
        el.value = '';
    });
}

function enterAnonData(data) {
    $$('#' + payment.currentMethod + '_saved_data span').each(function (el) {

        el.innerHTML = data[el.id];
    });
}