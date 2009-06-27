<?php
/**
 * @author Jason Paige <jason.paige@assertis.co.uk>
 * @date 28/Mar/2007
 * @version 0.5
 * @package email
 * Wrapper class for the htmlMimeMail package (http://www.phpguru.org/)
 */
require_once('htmlMimeMail.php');
/**
 * @var $to 15/05/2007email
 * @var $from array of the senders name and email address
 * @var $replyTo string with the reply to email address
 * @var $subject string string containg the subject of the email
 * @var $attachments array of File objects
 * @var $html string with the html version of the email
 * @var $text string with the plain text version of the email
 * @var $body containing the final output for the email
 * @var $footer containing the final output for the email
 * @var $headers containing the final output for the email
 * @var $mail htmlMimeMail object
 */
class Email {
	protected $to = array();
	protected $from = array();
	protected $replyTo;
	protected $subject;
	protected $attachments = array();
	protected $html;
	protected $text;
	protected $body;
	protected $footer;
	protected $headers;
	protected $mail;

  /**
   * Create a new instance of an Email
   * @return Email
   */
	public function __construct () {
        $this->mail = new htmlMimeMail();
	}

	/**
   * Add a recipient
   * @param string $email
   * @param string $name
   * @return void
   */
	public function addTo($email) {
		$this->to[] = $email;
	}

	/* FROM */
	/**
   * Set the From address
   * @param string $fromEmail
   * @param string $fromName
   * @return void
   */
	public function setFrom ($fromEmail, $fromName = "") {
		$this->from = array("email" => $fromEmail, "name" => $fromName);
		if ($this->replyTo["email"]=="") {
			$this->replyTo = $fromEmail;
		}
	}

  /* REPLY TO */
	/**
   * Set the Reply To address
   * @param string email
   * @return void
   */
	public function setReplyTo ($email) {
		$this->replyTo = $email;
	}

	/* SUBJECT */
	/**
   * Set the Subject of the email
   * @param string $subject
   * @return void
   */
	public function setSubject ($subject) {
		$this->subject = $subject;
	}

	/* ATTACHMENTS */
	/**
   * Add an Attachment to the email
   * @param File $file
   * @return int
   */
	public function addAttachment(File $file) {
	  $this->attachments[] = $file;
	  return count($this->attachments) - 1;
	}

	/**
   * Remove an attachment from the email
   * @param int $index
   * @return void
   */
	public function removeAttachment($index) {
    $this->attachments[$index] = null;
	}

	/* CONTENT */
	/**
	 * Set the HTML body
	 * @param string $html
	 */
  public function setHtml($html) {
    $this->html = $html;
    if ($this->text == "") {
			$this->text = strip_tags($html);
		}
	}

	/**
	 * Set the Plain Text body
	 * @param string $text
	 */
  public function setText($text) {
    $this->text = $text;
    if ($this->html == "") {
			$this->html = nl2br($text);
		}
	}

	/**
	 * Set the Footer
	 * @param string $text
	 */
  public function setFooter($text) {
    $this->footer = $text;
	}

	/* MISC */
	/**
	 * Prepair the email to be sent
	 * @return boolean
	 */
	private function build () {
    //$background = $mail->getFile('background.gif');
        foreach ($this->attachments as $a) {
			$attachment = $this->mail->getFile($a->getFileName());
            $this->mail->addAttachment($attachment, $a->getName().$a->getExtension(), $a->getMimeType());
		}
		$this->mail->setHtml($this->html, $this->text);
		//$mail->addHtmlImage($background, 'background.gif', 'image/gif');
		$this->mail->setReturnPath($this->replyTo);
		//$this->mail->setFrom("{$this->from['name']} <{$this->from['email']}>");
		$this->mail->setFrom("{$this->from['email']}");
		$this->mail->setSubject($this->subject);
		$this->mail->setHeader('X-Mailer', 'PHP Assertis Ltd');

	}

	/**
	 * Send the email
	 * @return void
	 */
	public function send () {
		try {
            $this->build();
            foreach ($this->to as $to) {
		        $this->mail->setSubject($this->subject);
                
		        if (!$this->mail->send("{$to}")) {
                    throw new AssEx();
                }
            }
		} 
		catch (AssEx $e) {
			$e->output("Error sending email");
            return false;
		}
		
    	return true;
	}
}
?>