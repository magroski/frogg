<?php

namespace Frogg;

/**
 * This class is used to validate (not sanitize) data inputs from the system and/or user.
 * Some of the most used regular expressions are defined above.
 */
class Validator
{

    ##ANYTHING
    const V_ALL = "/(.*)/";

    ##ACESS
    const V_LOGIN = "/^([a-zA-Z0-9\\-_.+]*)$/";
    const V_EMAIL = "/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/";
    const V_PASS  = "/^([A-Z,a-z,0-9,.,_,-,@,#,&,<,>]*)$/";
    const V_SHA1  = "/^[0-9a-f]{40,40}$/";

    ##APPLICATION DATA
    const V_ID        = "/^[0-9]{1,20}$/";
    const V_HEX       = "/^[0-9a-fA-F]*$/";
    const V_BOOLEAN   = "/^[0-1]{1,1}$/";
    const V_TITLE     = '/^([A-Z0-9a-z\-ºª,.!?:;@#%&*ÂÀÁÄÃâãàáäÊÈÉËêèéëÎÍÌÏîíìïÔÕÒÓÖôõòóöÛÙÚÜûúùüÇç\[\]+\\\\\'" ])*$/';
    const V_LINK      = '%^(?:(?:https?|ftp)://)?(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\n]*)?$%iu';
    const V_PERMALINK = "/([a-zA-Z0-9\\-_.,+])*/";
    const V_FILENAME  = "/([a-zA-Z0-9\\-_.,+])*.([a-zA-Z]{3,3})/";

    ##USER DATA
    const V_NAME     = "/^[A-Za-zÂÀÁÄÃâãàáäÊÈÉËêèéëÎÍÌÏîíìïÔÕÒÓÖôõòóöÛÙÚÜûúùüÇç. ]*$/";
    const V_MOBILE   = "/^[0-9]*$/";
    const V_SEX      = "/^([mfMF]{1,1})$/";
    const V_STATE_BR = "/^(GO|MT|MS|DF|AM|AC|RO|RR|AP|TO|PA|MA|PI|CE|RN|PB|PE|SE|AL|RS|SC|PR|ES|BA|SP|MG|RJ)$/";
    const V_CEP_BR   = "/^([0-9]{5,8})$/";

    ##SPECIFIC DATA
    const V_TWITTER   = "/^((http:\/\/)|(https:\/\/))?(www\.)?twitter\.com\/(#!\/)?([A-Za-z0-9\/])+$/";
    const V_FACEBOOK  = "/^((http:\/\/)|(https:\/\/))?(www\.)?facebook\.com\/([A-Z,a-z,0-9,@,%,#,!,&,*,+,:,?,_,=,.,\/]|[,]|[-])+$/";
    const V_YOUTUBE   = "/^((http:\/\/)|(https:\/\/))?(www\.)?((youtube\.com)|(youtu\.be))\/([A-Z,a-z,0-9,@,#,!,&,*,+,:,?,_,=,.,\/]|[,]|[-])+$/";
    const V_INSTAGRAM = "/^((http:\/\/)|(https:\/\/))?(www\.)?instagram\.com\/([A-Z,a-z,0-9,@,#,!,&,*,+,:,?,_,=,.,\/]|[,]|[-])+$/";

    /**
     * This function is used to validate a variable via a regular expression
     *
     * @param string $regex A regular expression. You can use one of the defined above or one of your own creation
     * @param string $var   String variable to be validated
     */
    public static function validate($regex, $var)
    {
        return preg_match($regex, $var);
    }

    /**
     * This function chekcs whether a given string is UTF-8
     *
     * @param string $string String to be checked
     */
    public static function isUTF8($string)
    {
        return mb_detect_encoding($string, 'UTF-8', true) == 'UTF-8';
    }

    /**
     * This function is used to sanitize a given string
     *
     * @param string $string String variable to be sanitized
     */
    public static function sanitize($string)
    {
        return htmlspecialchars(strip_tags(trim($string)), ENT_QUOTES);
    }

    /**
     *
     * Remove the protocol and the last '/' from a given URL
     *
     * @param string $url URL to be sanitized
     */
    public static function sanitizeUrl($url)
    {
        $url_no_protocol = $url;
        if (stristr($url, "https://")) {
            $tmp             = explode("https://", $url);
            $url_no_protocol = $tmp[1];
        } else if (stristr($url, "http://")) {
            $tmp             = explode("http://", $url);
            $url_no_protocol = $tmp[1];
        }

        if ($url_no_protocol == "") {
            return false;
        }

        return rtrim($url_no_protocol, '/');
    }

    /**
     * This function is used to validate a CPF (Cadastro de Pessoa F�sica) number.
     *
     * @param string $cpf CPF as string containing only numbers (no points or score)
     */
    public static function valCPF($cpf)
    {
        $cpf = str_replace('.', '', str_replace('-', '', $cpf));
        $cpf = str_pad(ereg_replace('[^0-9]', '', $cpf), 11, '0', STR_PAD_LEFT);

        if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
            return false;
        } else {
            for ($t = 9; $t < 11; $t++) {
                for ($d = 0, $c = 0; $c < $t; $c++) {
                    $d += $cpf{$c} * (($t + 1) - $c);
                }
                $d = ((10 * $d) % 11) % 10;
                if ($cpf{$c} != $d) {
                    return false;
                }
            }

            return true;
        }
    }

}