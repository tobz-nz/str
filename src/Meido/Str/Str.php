<?php namespace Meido\Str;

use Illuminate\Support\Facades\Config as Config;

define('MB_STRING', (int) function_exists('mb_get_info'));

class Str {

	/**
	 * Cache application encoding locally to save expensive calls to Config::get().
	 *
	 * @var string
	 */
	public static $encoding;


	/**
	 * Constructor
	 */
	public function __construct()
	{
		self::$encoding = 'utf-8';
	}

	/**
	 * Get the length of a string.
	 *
	 * @param  string  $value
	 * @return int
	 */
	public static function length($value)
	{
		return (MB_STRING) ? mb_strlen($value, self::$encoding) : strlen($value);
	}

	/**
	 * Convert a string to lowercase.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function lower($value)
	{
		return (MB_STRING) ? mb_strtolower($value, self::$encoding) : strtolower($value);
	}

	/**
	 * Convert a string to uppercase.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function upper($value)
	{
		return (MB_STRING) ? mb_strtoupper($value, self::$encoding) : strtoupper($value);
	}

	/**
	 * Convert a string to title case (ucwords equivalent).
	 *
	 * <code>
	 *		// Convert a string to title case
	 *		$title = Str::title('taylor otwell');
	 *
	 *		// Convert a multi-byte string to title case
	 *		$title = Str::title('νωθρού κυνός');
	 * </code>
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function title($value)
	{
		if (MB_STRING)
		{
			return mb_convert_case($value, MB_CASE_TITLE, self::$encoding);
		}

		return ucwords(strtolower($value));
	}

	/**
	 * Limit the number of characters in a string.
	 *
	 * @param  string  $value
	 * @param  int     $limit
	 * @param  string  $end
	 * @return string
	 */
	public static function limit($value, $limit = 100, $end = '...')
	{
		if ($this->length($value) <= $limit) return $value;

		if (MB_STRING)
		{
			return mb_substr($value, 0, $limit, self::$encoding).$end;
		}

		return substr($value, 0, $limit).$end;
	}

	/**
	 * Limit the number of chracters in a string including custom ending
	 *
	 * @param  string  $value
	 * @param  int     $limit
	 * @param  string  $end
	 * @return string
	 */
	public static function limit_exact($value, $limit = 100, $end = '...')
	{
		if ($this->length($value) <= $limit) return $value;

		$limit -= $this->length($end);

		return $this->limit($value, $limit, $end);
	}

	/**
	 * Limit the number of words in a string.
	 *
	 * @param  string  $value
	 * @param  int     $words
	 * @param  string  $end
	 * @return string
	 */
	public static function words($value, $words = 100, $end = '...')
	{
		if (trim($value) == '') return '';

		preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

		if ($this->length($value) == $this->length($matches[0]))
		{
			$end = '';
		}

		return rtrim($matches[0]).$end;
	}

	/**
	 * Convert a string to an underscored, camel-cased class name.
	 *
	 * This method is primarily used to format task and controller names.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function classify($value)
	{
		$search = array('_', '-', '.');

		return str_replace(' ', '_', $this->title(str_replace($search, ' ', $value)));
	}

	/**
	 * Return the "URI" style segments in a given string.
	 *
	 * @param  string  $value
	 * @return array
	 */
	public static function segments($value)
	{
		return array_diff(explode('/', trim($value, '/')), array(''));
	}

	/**
	 * Generate a random alpha or alpha-numeric string.
	 *
	 * @param  int	   $length
	 * @param  string  $type
	 * @return string
	 */
	public static function random($length, $type = 'alnum')
	{
		return substr(str_shuffle(str_repeat($this->pool($type), 5)), 0, $length);
	}

	/**
	 * Determine if a given string matches a given pattern.
	 *
	 * @param  string  $pattern
	 * @param  string  $value
	 * @return bool
	 */
	public static function is($pattern, $value)
	{
		// Asterisks are translated into zero-or-more regular expression wildcards
		// to make it convenient to check if the URI starts with a given pattern
		// such as "library/*". This is only done when not root.
		if ($pattern !== '/')
		{
			$pattern = str_replace('*', '(.*)', $pattern).'\z';
		}
		else
		{
			$pattern = '^/$';
		}

		return preg_match('#'.$pattern.'#', $value);
	}

	/**
	 * Get the character pool for a given type of random string.
	 *
	 * @param  string  $type
	 * @return string
	 */
	protected function pool($type)
	{
		switch ($type)
		{
			case 'alpha':
				return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			case 'alnum':
				return '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

			default:
				throw new \Exception("Invalid random string type [$type].");
		}
	}



	/**
	 * Generate a URL friendly "slug" from a given string.
	 *
	 * <code>
	 *		// Returns "this-is-my-blog-post"
	 *		$slug = Str::slug('This is my blog post!');
	 *
	 *		// Returns "this_is_my_blog_post"
	 *		$slug = Str::slug('This is my blog post!', '_');
	 * </code>
	 *
	 * @param  string  $title
	 * @param  string  $separator
	 * @return string
	 */
	public static function slug($title, $separator = '-')
	{
		$title = static::ascii($title);

		// Remove all characters that are not the separator, letters, numbers, or whitespace.
		$title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', self::lower($title));

		// Replace all separator characters and whitespace by a single separator
		$title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

		return trim($title, $separator);
	}

	/**
	 * Convert a string to 7-bit ASCII.
	 *
	 * This is helpful for converting UTF-8 strings for usage in URLs, etc.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function ascii($value)
	{
		$foreign = Config::get('str::strings.ascii');

		$value = preg_replace(array_keys($foreign), array_values($foreign), $value);

		return preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', '', $value);
	}

}
