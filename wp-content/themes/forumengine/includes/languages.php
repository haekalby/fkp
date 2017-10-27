<?php

/**
 *
 */
function et_setup_translation(){
	$translation = et_get_option('et_current_language');

	$path = FE_Language::get_lang_path();
	if ( file_exists( $path . '/' . $translation . '.mo' ) ){
		$result = load_textdomain( ET_DOMAIN, $path . '/' . $translation . '.mo' );
	}
}
add_action('after_setup_theme', 'et_setup_translation');

function et_lang_path(){
	if ( !file_exists( WP_CONTENT_DIR . '/et-content' ) )
		mkdir( WP_CONTENT_DIR . '/et-content' );

	if ( !file_exists( THEME_CONTENT_DIR ) )
		mkdir( THEME_CONTENT_DIR );

	$lang_dir 		= THEME_CONTENT_DIR . '/lang';

	if ( !file_exists( $lang_dir )  )
		mkdir( $lang_dir );

	return $lang_dir;
}

if(!class_exists('FE_Language')){
class FE_Language {

	var $rules = array(
		'_' => array('string'),
		'__' => array('string'),
		'_e' => array('string'),
		'_c' => array('string'),
		'_n' => array('singular', 'plural'),
		'_n_noop' => array('singular', 'plural'),
		'_nc' => array('singular', 'plural'),
		'__ngettext' => array('singular', 'plural'),
		'__ngettext_noop' => array('singular', 'plural'),
		'_x' => array('string', 'context'),
		'_ex' => array('string', 'context'),
		'_nx' => array('singular', 'plural', null, 'context'),
		'_nx_noop' => array('singular', 'plural', 'context'),
		'esc_attr__' => array('string'),
		'esc_html__' => array('string'),
		'esc_attr_e' => array('string'),
		'esc_html_e' => array('string'),
		'esc_attr_x' => array('string', 'context'),
		'esc_html_x' => array('string', 'context'),
		'comments_number_link' => array('string', 'singular', 'plural'),
	);

	public function __construct(){
		$this->extractor = new StringExtractor( $this->rules );
	}

	static function get_lang_path(){
		if ( !file_exists( WP_CONTENT_DIR . '/et-content' ) )
			mkdir( WP_CONTENT_DIR . '/et-content' );

		if ( !file_exists( THEME_CONTENT_DIR ) )
			mkdir( THEME_CONTENT_DIR );

		$lang_dir 		= THEME_CONTENT_DIR . '/lang';

		if ( !file_exists( $lang_dir )  )
			mkdir( $lang_dir );

		return $lang_dir;
	}

	public function get_strings(){
		if ( !class_exists('StringExtractor') ){
			include TEMPLATEPATH . '/includes/makePOT/extract/extract.php';
		}

		$originals = $this->extractor->extract_from_directory( TEMPLATEPATH );
		return $originals;
	}

	/**
	 * Get translation strings from lang file
	 * @param $lang the lang name
	 * @return Translation entries if success, otherwise false
	 */
	public function get_translation_from_file($lang){
		$path 		= self::get_lang_path();
		$input_file = $path . '/' . $lang . '.po';

		if (file_exists( $input_file )){
			$updated = $this->update_translation($lang);

			return $updated->entries;
		} else {
			return false;
		}
	}

	protected function update_translation($lang){
		$path 		= self::get_lang_path();
		$input_file = $path . '/' . $lang . '.po';
		$input_file_mo = $path . '/' . $lang . '.mo';

		if (file_exists( $input_file )){
			$po = new PO;
			$po->import_from_file( $input_file );

			// If there is new string to translate,
			$originals 	= $this->get_strings();
			foreach ($originals->entries as $key => $entry) {
				if ( !isset($po->entries[$key]) ){
					$po->entries[$key] = $entry;
				}
			}

			// export to file
			$po->export_to_file( $input_file );

			// create binary version
			$this->export_binary($lang);

			return $po;
		} else {
			return false;
		}
	}

	public function export_binary($lang){
		$path 			= self::get_lang_path();
		$input_file 	= $path . '/' . $lang . '.po';
		$output_file 	= $path . '/' . $lang . '.mo';

		if (file_exists( $input_file )){
			$mo = new MO;
			if ( file_exists( $output_file ) ){
				$mo->import_from_file($output_file);
			} else {
				$mo->set_header( 'Project-Id-Version', THEME_NAME . ' ' . ET_VERSION );
				$mo->set_header( 'Report-Msgid-Bugs-To', 'enginethemes.com' );
				$mo->set_header( 'POT-Creation-Date', gmdate( 'Y-m-d H:i:s+00:00' ) );
				$mo->set_header( 'MIME-Version', '1.0' );
				$mo->set_header( 'Content-Type', 'text/plain; charset=UTF-8' );
				$mo->set_header( 'Content-Transfer-Encoding', '8bit' );
				$mo->set_header( 'PO-Revision-Date', '2010-MO-DA HO:MI+ZONE' );
				$mo->set_header( 'Last-Translator', 'EngineThemes <contact@enginethemes.com>' );
				$mo->set_header( 'Language-Team', 'EngineThemes <contact@enginethemes.com>' );
			}

			// prepare po file
			$po = new PO;
			$po->import_from_file( $input_file );

			// merge translations
			$mo->entries = $po->entries;
			$mo->export_to_file($output_file);
		} else {
			return false;
		}
	}

	public function add_lang_file($filename){
		// get strings first
		$originals 	= $this->get_strings();

		$path 			= self::get_lang_path();
		$output_file 	= $path . '/' . basename($filename);

		// create po file
		$po = new PO;
		$po->entries = $originals->entries;
		$po->set_header( 'Project-Id-Version', THEME_NAME . ' ' . ET_VERSION );
		$po->set_header( 'Report-Msgid-Bugs-To', 'enginethemes.com' );
		$po->set_header( 'POT-Creation-Date', gmdate( 'Y-m-d H:i:s+00:00' ) );
		$po->set_header( 'MIME-Version', '1.0' );
		$po->set_header( 'Content-Type', 'text/plain; charset=UTF-8' );
		$po->set_header( 'Content-Transfer-Encoding', '8bit' );
		$po->set_header( 'PO-Revision-Date', '2010-MO-DA HO:MI+ZONE' );
		$po->set_header( 'Last-Translator', 'EngineThemes <contact@enginethemes.com>' );
		$po->set_header( 'Language-Team', 'EngineThemes <contact@enginethemes.com>' );
		$po->set_comment_before_headers( 'Copyright (C) 2010 {package-name}\nThis file is distributed under the same license as the {package-name} package.' );
		$po->export_to_file( $output_file . '.po' );

		// create mo file
		$this->export_binary($filename);

		return true;
	}

	/**
	 *
	 */
	public function save_lang($filename, $new_translation){
		$path 			= self::get_lang_path();
		$output_file 	= $path . '/' . basename($filename);
		$output_file_mo = $output_file . '.mo';
		$output_file_po = $output_file . '.po';

		if ( !file_exists( $output_file_po ) ){
			$this->add_lang_file($filename);
		}

		// create po file
		$po = new PO;
		$po->import_from_file( $output_file_po );

		// create mo file
		$translation = $po->entries;
		foreach ($new_translation as $translation) {
			$entry = new Translation_Entry(array (
					'singular' => trim ( stripcslashes($translation['singular']),''),
					'translations' => array( trim ( stripcslashes($translation['translations']), ''))
				)
			);

			// if
			if ( isset( $po->entries[$entry->key()] ) ){
				$po->entries[$entry->key()]->translations = $entry->translations;
			}
		}
		$po->export_to_file( $output_file_po );

		// create mo file
		$this->export_binary($filename);

		return true;
	}
}
}
?>