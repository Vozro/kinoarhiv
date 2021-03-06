<?php
/**
 * @package     Kinoarhiv.Administrator
 * @subpackage  com_kinoarhiv
 *
 * @copyright   Copyright (C) 2010 Libra.ms. All rights reserved.
 * @license     GNU General Public License version 2 or later
 * @url            http://киноархив.com/
 */

defined('_JEXEC') or die;

/**
 * Class KALanguage
 *
 * @since  3.0
 */
class KALanguage extends JLanguage
{
	/**
	 * Method return list of languages for subtitles for video files.
	 * List of languages according to http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
	 *
	 * @return    array
	 */
	public static function listOfLanguages()
	{
		$list = array(
			'ab' => 'Abkhaz', 'aa' => 'Afar', 'af' => 'Afrikaans', 'ak' => 'Akan',
			'sq' => 'Albanian', 'am' => 'Amharic', 'ar' => 'Arabic', 'an' => 'Aragonese',
			'hy' => 'Armenian', 'as' => 'Assamese', 'av' => 'Avaric', 'ae' => 'Avestan',
			'ay' => 'Aymara', 'az' => 'Azerbaijani', 'bm' => 'Bambara', 'ba' => 'Bashkir',
			'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali', 'bh' => 'Bihari',
			'bi' => 'Bislama', 'bs' => 'Bosnian', 'br' => 'Breton', 'bg' => 'Bulgarian',
			'my' => 'Burmese', 'ca' => 'Catalan', 'ch' => 'Chamorro', 'ce' => 'Chechen',
			'ny' => 'Chichewa', 'zh' => 'Chinese', 'cv' => 'Chuvash', 'kw' => 'Cornish',
			'co' => 'Corsican', 'cr' => 'Cree', 'hr' => 'Croatian', 'cs' => 'Czech',
			'da' => 'Danish', 'dv' => 'Divehi', 'nl' => 'Dutch', 'dz' => 'Dzongkha',
			'en' => 'English', 'eo' => 'Esperanto', 'et' => 'Estonian', 'ee' => 'Ewe',
			'fo' => 'Faroese', 'fj' => 'Fijian', 'fi' => 'Finnish', 'fr' => 'French',
			'ff' => 'Fula', 'gl' => 'Galician', 'ka' => 'Georgian', 'de' => 'German',
			'el' => 'Greek', 'gn' => 'Guaraní', 'gu' => 'Gujarati', 'ht' => 'Haitian',
			'ha' => 'Hausa', 'he' => 'Hebrew', 'hz' => 'Herero', 'hi' => 'Hindi',
			'ho' => 'Hiri Motu', 'hu' => 'Hungarian', 'ia' => 'Interlingua', 'id' => 'Indonesian',
			'ie' => 'Interlingue', 'ga' => 'Irish', 'ig' => 'Igbo', 'ik' => 'Inupiaq',
			'io' => 'Ido', 'is' => 'Icelandic', 'it' => 'Italian', 'iu' => 'Inuktitut',
			'ja' => 'Japanese', 'jv' => 'Javanese', 'kl' => 'Kalaallisut', 'kn' => 'Kannada',
			'kr' => 'Kanuri', 'ks' => 'Kashmiri', 'kk' => 'Kazakh', 'km' => 'Khmer',
			'ki' => 'Kikuyu', 'rw' => 'Kinyarwanda', 'ky' => 'Kyrgyz', 'kv' => 'Komi',
			'kg' => 'Kongo', 'ko' => 'Korean', 'ku' => 'Kurdish', 'kj' => 'Kwanyama',
			'la' => 'Latin', 'lb' => 'Luxembourgish', 'lg' => 'Ganda', 'li' => 'Limburgish',
			'ln' => 'Lingala', 'lo' => 'Lao', 'lt' => 'Lithuanian', 'lu' => 'Luba-Katanga',
			'lv' => 'Latvian', 'gv' => 'Manx', 'mk' => 'Macedonian', 'mg' => 'Malagasy',
			'ms' => 'Malay', 'ml' => 'Malayalam', 'mt' => 'Maltese', 'mi' => 'Māori',
			'mr' => 'Marathi (Marāṭhī)', 'mh' => 'Marshallese', 'mn' => 'Mongolian', 'na' => 'Nauru',
			'nv' => 'Navajo', 'nb' => 'Norwegian Bokmål', 'nd' => 'North Ndebele', 'ne' => 'Nepali',
			'ng' => 'Ndonga', 'nn' => 'Norwegian Nynorsk', 'no' => 'Norwegian', 'ii' => 'Nuosu',
			'nr' => 'South Ndebele', 'oc' => 'Occitan', 'oj' => 'Ojibwe', 'cu' => 'Old Church Slavonic',
			'om' => 'Oromo', 'or' => 'Oriya', 'os' => 'Ossetian', 'pa' => 'Panjabi',
			'pi' => 'Pāli', 'fa' => 'Persian', 'pl' => 'Polish', 'ps' => 'Pashto',
			'pt' => 'Portuguese', 'qu' => 'Quechua', 'rm' => 'Romansh', 'rn' => 'Kirundi',
			'ro' => 'Romanian', 'ru' => 'Russian', 'sa' => 'Sanskrit', 'sc' => 'Sardinian',
			'sd' => 'Sindhi', 'se' => 'Northern Sami', 'sm' => 'Samoan', 'sg' => 'Sango',
			'sr' => 'Serbian', 'gd' => 'Scottish Gaelic', 'sn' => 'Shona', 'si' => 'Sinhala',
			'sk' => 'Slovak', 'sl' => 'Slovene', 'so' => 'Somali', 'st' => 'Southern Sotho',
			'es' => 'Spanish', 'su' => 'Sundanese', 'sw' => 'Swahili',
			'ss' => 'Swati', 'sv' => 'Swedish', 'ta' => 'Tamil', 'te' => 'Telugu',
			'tg' => 'Tajik', 'th' => 'Thai', 'ti' => 'Tigrinya', 'bo' => 'Tibetan',
			'tk' => 'Turkmen', 'tl' => 'Tagalog', 'tn' => 'Tswana', 'to' => 'Tonga',
			'tr' => 'Turkish', 'ts' => 'Tsonga', 'tt' => 'Tatar', 'tw' => 'Twi',
			'ty' => 'Tahitian', 'ug' => 'Uyghur', 'uk' => 'Ukrainian', 'ur' => 'Urdu',
			'uz' => 'Uzbek', 've' => 'Venda', 'vi' => 'Vietnamese', 'vo' => 'Volapük',
			'wa' => 'Walloon', 'cy' => 'Welsh', 'wo' => 'Wolof', 'fy' => 'Western Frisian',
			'xh' => 'Xhosa', 'yi' => 'Yiddish', 'yo' => 'Yoruba', 'za' => 'Zhuang',
			'zu' => 'Zulu'
		);

		return $list;
	}

	/**
	 * Load language files for JS scripts
	 *
	 * @param   string   $file         Part of the filename w/o language tag and extension
	 * @param   string   $jhtml        Use JHtml::script() to load
	 * @param   string   $script_type  Type of the script(folder name in assets/js/i8n/)
	 * @param   boolean  $frontend     Load language file from the frontend if set to true
	 * @param   string   $separator    Separator, which is used for split two-letter language code and two-letter country
	 *                                 code. Usually separated by hyphens('-'). E.g. en-US, ru-RU
	 *
	 * @return mixed String or void
	 */
	public static function getScriptLanguage($file, $jhtml, $script_type, $frontend, $separator='-')
	{
		$lang = JFactory::getLanguage()->getTag();
		$filename = $file . $lang . '.js';

		if ($frontend)
		{
			$basepath = JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_kinoarhiv' . DIRECTORY_SEPARATOR;
			$url = JUri::root();
		}
		else
		{
			$basepath = JPATH_COMPONENT . DIRECTORY_SEPARATOR;
			$url = JUri::base();
		}

		$path = $basepath . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $script_type;

		if (is_file($path . DIRECTORY_SEPARATOR . $filename))
		{
			if ($jhtml)
			{
				JHtml::_('script', $url . 'components/com_kinoarhiv/assets/js/i18n/' . $script_type . '/' . $filename);
			}
			else
			{
				echo '<script src="' . $url . 'components/com_kinoarhiv/assets/js/i18n/' . $script_type . '/' . $filename . '" type="text/javascript"></script>';
			}
		}
		elseif (is_file($path . DIRECTORY_SEPARATOR . $file . substr($lang, 0, 2) . '.js'))
		{
			if ($jhtml)
			{
				JHtml::_('script', $url . 'components/com_kinoarhiv/assets/js/i18n/' . $script_type . '/' . $file . substr($lang, 0, 2) . '.js');
			}
			else
			{
				echo '<script
				src="' . $url . 'components/com_kinoarhiv/assets/js/i18n/' . $script_type . '/' . $file . substr($lang, 0, 2) . '.js"
				type="text/javascript"></script>';
			}
		}
		else
		{
			if ($jhtml)
			{
				JHtml::_('script', $url . 'components/com_kinoarhiv/assets/js/i18n/' . $script_type . '/' . $file
					. str_replace('-', $separator, substr($lang, 0, 2)) . '.js');
			}
			else
			{
				echo '<script
				src="' . $url . 'components/com_kinoarhiv/assets/js/i18n/' . $script_type . '/' . $file
					. str_replace('-', $separator, substr($lang, 0, 2)) . '.js"
				type="text/javascript"></script>';
			}
		}
	}
}
