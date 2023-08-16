<?php

namespace manne65hd;

 class F3_Menu {

	//@{ Default Styles
	const
		STYLE_TRUE = 'background-color: green; color: white; font-weight: bolder;',
		STYLE_FALSE = 'background-color: red; color:white; font-weight: bolder;';
	//@}

	protected
        //! The FatFree-Object
        $f3,
        //! current VARIABLE-name
        $current_var,
        //! current VARIABLE-value
        $current_value,
		//! current VARIABLE-type
        $current_type,
		//! current DUMP-title
        $current_title,
		//! current file that DUMPS data
        $current_filename,
		//! current line that DUMPS data
        $current_line,
		//! Style-definition for boolean:TRUE
		$style_true,
		//! Style-definition for boolean:FALSE
        $style_false;



	public function __construct($dumper_options = array()) {

        $this->f3 = \Base::instance();

        // detect if the package is included via SYMLINK and "report" to Framework
        // I will only use this in the EARLY stage of development!
        if (str_contains(dirname(__FILE__), 'local_pkgdev_manne65hd')) {
            $this->f3->push('dev_info', 'manne65hd/F3_UserGroupMenu');
        } 

        echo 'The Menu via Symlink WAVES @ you ;-) <br />';

	}

}
