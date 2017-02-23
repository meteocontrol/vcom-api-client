<?php

class Meteocontrol_Sniffs_NamingConventions_ValidVariableNameSniff implements PHP_CodeSniffer_Sniff {

    public function register() {
        return array(T_VARIABLE);
    }

    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
        static $reportedErrors = array();
        $tokens = $phpcsFile->getTokens();
        $name = substr($tokens[$stackPtr]['content'], 1); // Cut the $ sign.

        if (in_array($name, array_merge(array_keys($GLOBALS), array('_SESSION', '_REQUEST')))) {
            return;
        }

        // Cut the _ 'protected/private' modifier.
        if ($name[0] === '_') {
            $name = substr($name, 1);
        }

        if (isset($reportedErrors[$phpcsFile->getFilename()][$name])) {
            return;
        }

        if (strlen($name) > 1 && !preg_match('/^[a-z][a-zA-Z0-9]*$/', $name)) {
            $phpcsFile->addWarning(
                'Variable "%s" is not in valid camel caps format',
                $stackPtr,
                '',
                $tokens[$stackPtr]['content']
            );
            $reportedErrors[$phpcsFile->getFilename()][$name] = true;
        }
    }
}
