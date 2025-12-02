<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Email Configuration
 * 
 * IMPORTANT: Email credentials are stored in the .env file for security.
 * Update the following variables in your .env file:
 * - email.fromEmail
 * - email.fromName
 * - email.SMTPHost
 * - email.SMTPUser
 * - email.SMTPPass
 * - email.SMTPPort
 * - email.SMTPCrypto
 */
class Email extends BaseConfig
{
	/**
	 * @var string
	 */
	public $fromEmail;

	/**
	 * @var string
	 */
	public $fromName;

	/**
	 * @var string
	 */
	public $recipients;

	/**
	 * The "user agent"
	 *
	 * @var string
	 */
	public $userAgent = 'CodeIgniter';

	/**
	 * The mail sending protocol: mail, sendmail, smtp
	 *
	 * @var string
	 */
	public $protocol = 'smtp';

	/**
	 * The server path to Sendmail.
	 *
	 * @var string
	 */
	public $mailPath = '/usr/sbin/sendmail';

	/**
	 * SMTP Server Address
	 *
	 * @var string
	 */
	public $SMTPHost;

	/**
	 * SMTP Username
	 *
	 * @var string
	 */
	public $SMTPUser;

	/**
	 * SMTP Password
	 *
	 * @var string
	 */
	public $SMTPPass;

	/**
	 * SMTP Port
	 *
	 * @var integer
	 */
	public $SMTPPort;

	/**
	 * SMTP Timeout (in seconds)
	 *
	 * @var integer
	 */
	public $SMTPTimeout = 5;

	/**
	 * Enable persistent SMTP connections
	 *
	 * @var boolean
	 */
	public $SMTPKeepAlive = false;

	/**
	 * SMTP Encryption. Either tls or ssl
	 *
	 * @var string
	 */
	public $SMTPCrypto;

	/**
	 * Enable word-wrap
	 *
	 * @var boolean
	 */
	public $wordWrap = true;

	/**
	 * Character count to wrap at
	 *
	 * @var integer
	 */
	public $wrapChars = 76;

	/**
	 * Type of mail, either 'text' or 'html'
	 *
	 * @var string
	 */
	public $mailType = 'html';

	/**
	 * Character set (utf-8, iso-8859-1, etc.)
	 *
	 * @var string
	 */
	public $charset = 'UTF-8';

	/**
	 * Whether to validate the email address
	 *
	 * @var boolean
	 */
	public $validate = false;

	/**
	 * Email Priority. 1 = highest. 5 = lowest. 3 = normal
	 *
	 * @var integer
	 */
	public $priority = 3;

	/**
	 * Newline character. (Use “\r\n” to comply with RFC 822)
	 *
	 * @var string
	 */
	public $CRLF = "\r\n";

	/**
	 * Newline character. (Use “\r\n” to comply with RFC 822)
	 *
	 * @var string
	 */
	public $newline = "\r\n";

	/**
	 * Enable BCC Batch Mode.
	 *
	 * @var boolean
	 */
	public $BCCBatchMode = false;

	/**
	 * Number of emails in each BCC batch
	 *
	 * @var integer
	 */
	public $BCCBatchSize = 200;

	/**
	 * Enable notify message from server
	 *
	 * @var boolean
	 */
	public $DSN = false;

	/**
	 * Constructor - Load email settings from environment
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Load email configuration from environment variables
		$this->fromEmail   = env('email.fromEmail', 'noreply@ascot.edu.ph');
		$this->fromName    = env('email.fromName', 'ASCOT InfoVault');
		$this->SMTPHost    = env('email.SMTPHost', 'smtp.gmail.com');
		$this->SMTPUser    = env('email.SMTPUser', '');
		$this->SMTPPass    = env('email.SMTPPass', '');
		$this->SMTPPort    = env('email.SMTPPort', 587);
		$this->SMTPCrypto  = env('email.SMTPCrypto', 'tls');
	}

}
