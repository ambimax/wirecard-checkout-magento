<?php
$installer = $this;
$installer->startSetup();

$installer->addAttribute(
    "customer",
    "company_trade_reg_number",
    array(
        "type" => "varchar",
        "backend" => "",
        "label" => "Company Trade Registration Number",
        "input" => "text",
        "source" => "",
        "visible" => true,
        "required" => false,
        "default" => "",
        "frontend" => "",
        "unique" => false,
        "note" => ""

    )
);

$used_in_forms = array(
    "adminhtml_customer",
    "checkout_register",
    "customer_account_create",
    "customer_account_edit",
    "adminhtml_checkout"
);

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "company_trade_reg_number");
$attribute->setData("used_in_forms", $used_in_forms)
    ->setData("is_used_for_customer_segment", true)
    ->setData("is_system", 0)
    ->setData("is_user_defined", 1)
    ->setData("is_visible", 1)
    ->setData("sort_order", 999);
$attribute->save();

$installer->endSetup();