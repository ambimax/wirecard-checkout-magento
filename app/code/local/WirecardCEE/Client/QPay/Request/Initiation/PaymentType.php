<?php
/*
    Die vorliegende Software ist Eigentum von Wirecard CEE und daher vertraulich zu behandeln.
    Jegliche Weitergabe an dritte, in welcher Form auch immer, ist unzulaessig.

    Software & Service Copyright (C) by
    Wirecard Central Eastern Europe GmbH,
    FB-Nr: FN 195599 x, http://www.wirecard.at
*/

/**
 * 
 */
class WirecardCEE_Client_QPay_Request_Initiation_PaymentType
{
    const SELECT = 'SELECT';
    const CCARD = 'CCARD';
    const CCARD_MOTO = 'CCARD-MOTO';
    const MAESTRO = 'MAESTRO';
    const PBX = 'PBX';
    const PSC = 'PSC';
    const EPS = 'EPS';
    const ELV = 'ELV';
    const QUICK = 'QUICK';
    const IDL = 'IDL';
    const GIROPAY = 'GIROPAY';
    const PAYPAL = 'PAYPAL';
    const SOFORTUEBERWEISUNG = 'SOFORTUEBERWEISUNG';
    const C2P = 'C2P';
    const BMC = 'BMC';
    const INVOICE = 'INVOICE';
    const P24 = 'P24';
    const MONETA = 'MONETA';
    const POLI = 'POLI';
    const INSTANTBANK = 'INSTANTBANK';
    const EKONTO = 'EKONTO';
    const MPASS = 'MPASS';
    const SKRILLDIRECT = 'SKRILLDIRECT';
    const SKRILLWALLET = 'SKRILLWALLET';

    /**
     * array of eps financial institutions
     * @var string[]
     */
    private static $_eps_financial_institutions = Array(
                                'BA-CA'         =>  'Bank Austria', 
                                'Spardat|BB'    =>  'Bank Burgenland',
                                'ARZ|BAF'       =>  'Bank f&uuml; &Auml;rzte und Freie Berufe',
                                'ARC|BCS'       =>  'Bankhaus Carl Sp&auml;ngler &amp; Co. AG', 
                                'Bawag|B'       =>  'BAWAG', 
                                'ARZ|VB'        =>  'Die &ouml;stereischischen Volksbanken',
                                'Bawag|E'       =>  'easyBank',
                                'Spardat|EBS'   =>  'Erste Bank und Sparkassen',
                                'ARZ|GB'        =>  'G&auml;rtnerbank',
                                'ARZ|HAA'       =>  'Hypo Alpe-Adria Bank AG',
                                'ARZ|HI'        =>  'Hypo Investmentbank AG',
                                'Hypo-Racon|O'  =>  'Hypo Ober&ouml;sterreich',
                                'Hypo-Racon|S'  =>  'Hypo Salzburg',
                                'Hypo-Racon|ST' =>  'Hypo Steiermark',
                                'ARZ|HTB'       =>  'Hypo Tirol Bank AG',
                                'ARZ|IB'        =>  'Immo-Bank',
                                'ARZ|IKB'       =>  'Investkredit Bank AG',
                                'ARZ|NLH'       =>  'Niester&ouml;sterreichische Landes-Hypothekenbank AG',
                                'ARZ|AB'        =>  '&Ouml;sterreichische Apothekerbank',
                                'Bawag|P'       =>  'PSK Bank',
                                'Racon'         =>  'Raiffeisen Bank',
                                'Bawag|S'       =>  'Sparda Bank',
                                'ARZ|VLH'       =>  'Vorarlberger Landes- und Hypothekerbank AG',
                                );
    
    /**
     * array of iDEAL financial institutions
     * @var string[]
     */
    private static $_idl_financial_institutions = Array(
                                'ABNAMROBANK'   =>  'ABN AMRO Bank',
                                'POSTBANK'      =>  'Postbank',
                                'RABOBANK'      =>  'Rabobank',
                                'SNSBANK'       =>  'SNS Bank',
                                'ASNBANK'       =>  'ASN Bank',
                                'REGIOBANK'     =>  'SNS Regio Bank',
                                'TRIODOSBANK'   =>  'Triodos Bank',
                                'VANLANSCHOT'   =>  'Van Lanschot Bank',
                                );
    
    private function __construct($name) 
    {
        //private constructor
    }

    /**
     * check if the given paymenttype has financial institions
     * @param string $paymentType
     * @return bool 
     */
    public static function hasFinancialInstitutions($paymentType)
    {
        if($paymentType == self::EPS || 
           $paymentType == self::IDL )
            return true;
        
        return false;
    }
    
    /**
     * the an array of financial institutions for the given paymenttype.
     * @param string $paymentType
     * @return string[]
     */
    public static function getFinancialInstitutions($paymentType)
    {
        switch($paymentType)
        {
            case self::EPS:
                return self::$_eps_financial_institutions;
                break;
            case self::IDL:
                return self::$_idl_financial_institutions;
                break;
            default:
                return Array();
                break;
        }
    }
}