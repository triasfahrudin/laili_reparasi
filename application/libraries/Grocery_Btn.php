<?php
 if (!defined('BASEPATH')) {
     exit('No direct script access allowed');
 }

class Grocery_Btn
{
    private $buttons = array();

    public function __construct()
    {
        $this->ci = &get_instance();

        log_message('debug', 'Grocery_CRUD_Button Class Initialized');
    }

    public function push($url_action, $title, $span_class = 'add')
    {
        // no page or href provided
        if (!$url_action) {
            return;
        }

        // push button
        array_push($this->buttons, array('url_action' => $url_action, 'title' => $title, 'span_class' => $span_class));
    }

    public function show()
    {
        if ($this->buttons) {

            // set output variable
            $output = '<!-- Grocery CRUD Button -->' . PHP_EOL;

            // construct output
            foreach ($this->buttons as $key => $btn) {
                $output .= "$('.tDiv_tambahan').append('".
                            '<a href="'.$btn['url_action'].'" title="'.$btn['title'].'" class="add-anchor add_button">'.
                            ' <div class="fbutton">'.
                            '   <div>'.
                            '     <span class="'.$btn['span_class'].'">'.$btn['title'].'</span>'.
                            '    </div>'.
                            '   </div>'.
                            '</a>'.
                            "<div class=\"btnseparator\"></div>');" . PHP_EOL;
            }

            // return output
            return $output.PHP_EOL;
        }

        // no crumbs
        return '';
    }
}
