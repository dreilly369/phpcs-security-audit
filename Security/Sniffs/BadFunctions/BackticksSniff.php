<?php


class Security_Sniffs_BadFunctions_BackticksSniff implements PHP_CodeSniffer_Sniff {

	/**
	* Returns the token types that this sniff is interested in.
	*
	* @return array(int)
	*/
	public function register() {
		return array(T_BACKTICK);
	}

	/**
	* Framework or CMS used. Must be a class under Security_Sniffs.
	*
	* @var String
	*/
	public $CmsFramework = NULL;

	/**
	* Processes the tokens that this sniff is interested in.
	*
	* @param PHP_CodeSniffer_File $phpcsFile The file where the token was found.
	* @param int                  $stackPtr  The position in the stack where
	*                                        the token was found.
	*
	* @return void
	*/
	public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr) {
		$utils = Security_Sniffs_UtilsFactory::getInstance($this->CmsFramework);
		$tokens = $phpcsFile->getTokens();
        $closer = $phpcsFile->findNext(T_BACKTICK, $stackPtr + 1, null, false, null, true);
		if (!$closer) {
			return;
		}
        $s = $stackPtr + 1;
		$s = $phpcsFile->findNext(T_VARIABLE, $s, $closer);
        if ($s) {
			$msg = 'System execution with backticks detected with dynamic parameter';
			if ($utils::is_direct_user_input($tokens[$s]['content'])) {
				$phpcsFile->addError($msg . ' directly from user input', $stackPtr, 'ErrSystemExec');
			} else {
				$phpcsFile->addWarning($msg, $stackPtr, 'WarnSystemExec');
			}
		}

	}

}


?>
