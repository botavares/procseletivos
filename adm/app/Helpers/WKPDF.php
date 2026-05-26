<?php defined('BASEPATH') or exit('No direct script access allowed');

use mikehaertl\wkhtmlto\Pdf as HtmlToPDF;

/**
 * A simple codeigniter library to help setup phpwkhtmltopdf
 * @link https://gist.github.com/DykiSA/
 */

class WKPDF
{
    private $CI;
    private $error;
    private $options;
    
    /**
     * intialize the class WKPDF
     * @param array $options      override options for phpwkhtmltopdf
     */
    public function __construct($options = [])
    {
        $this->CI =& get_instance();

        $this->initialize($options);
    }

    /**
     * initialize the library
     * @param array $options      override options for phpwkhtmltopdf
     */
    public function initialize($options = []) {
        // get options
        $this->CI->load->config('phpwkhtmltopdf');
        $this->options = $this->CI->config->item('phpwkhtmltopdf');

        if (is_array($options) && !empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
    }
    
    /**
     * generating html
     * @param string $content       content of the pdf file
     * @param string $filename      will be shown as downloaded file
     * @param bool   $is_download   download = true/false
     * @return bool                 true if sucess otherwise false
     */
    public function generate($content, $filename, $is_download)
    {
        // clear error messgae
        $this->error = null;
        // init phpwkhtmltopdf
        $pdf = new HtmlToPDF($this->options);
        // add content
        $pdf->addPage($content);
        // generate pdf
        $is_success = false;
        if ($is_download) {
            $is_success = $pdf->send($filename);
        } else {
            $is_success = $pdf->send();
        }
        
        if (!$is_success) {
            // update error message
            $this->error = $pdf->getError();
        }
        return $is_success;
    }

    /**
     * get error message from last action
     * @return string       error message
     */
    public function getError()
    {
        return $this->error;
    }
    
}