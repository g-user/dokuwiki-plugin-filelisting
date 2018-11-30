<?php
/**
 * DokuWiki Plugin filelisting (Syntax Component)
 *
 * @license GPL 2 http://www.gnu.org/licenses/gpl-2.0.html
 * @author  Szymon Olewniczak <dokuwiki@cosmocode.de>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

class syntax_plugin_filelisting extends DokuWiki_Syntax_Plugin {
    /**
     * @return string Syntax mode type
     */
    public function getType() {
        return 'substition';
    }
    /**
     * @return string Paragraph type
     */
    public function getPType() {
        return 'block';
    }
    /**
     * @return int Sort order - Low numbers go before high numbers
     */
    public function getSort() {
        return 13;
    }

    /**
     * Connect lookup pattern to lexer.
     *
     * @param string $mode Parser mode
     */
    public function connectTo($mode) {
        $this->Lexer->addSpecialPattern('{{filelisting>?.*?}}',$mode,'plugin_filelisting');
    }

    /**
     * Handle matches of the filelisting syntax
     *
     * @param string          $match   The match of the syntax
     * @param int             $state   The state of the handler
     * @param int             $pos     The position in the document
     * @param Doku_Handler    $handler The handler
     * @return array Data for the renderer
     */
    public function handle($match, $state, $pos, Doku_Handler $handler){
        global $ID;
        global $conf;

        $param = substr($match, strlen('{{filelisting'), -strlen('}}'));
        $cur_ns = getNS($ID);
        //no namespace provided
        if (strlen($param) == 0) {
            return array($cur_ns);
        }
        //remove '>' from the path
        $ns = substr($param, 1);
        $abs_ns = resolve_id($cur_ns, $ns);
        $dir = str_replace(':','/',$abs_ns);
        $abs_dir = $conf['mediadir'].'/'.utf8_encodeFN($dir);
        if (!file_exists($abs_dir)) {
            msg("filelisting: No namespace $ns", -1);
            return array($cur_ns);
        }

        return array($abs_ns);
    }

    /**
     * Render xhtml output or metadata
     *
     * @param string         $mode      Renderer mode (supported modes: xhtml)
     * @param Doku_Renderer  $renderer  The renderer
     * @param array          $data      The data from the handler() function
     * @return bool If rendering was successful.
     */
    public function render($mode, Doku_Renderer $renderer, $data) {
        if($mode != 'xhtml') return false;

        list($ns) = $data;

        /** @var helper_plugin_filelisting $hlp */
        $hlp = plugin_load('helper', 'filelisting');

        $renderer->doc .= $hlp->tpl_filelisting($ns,false);
        return true;
    }
}

// vim:ts=4:sw=4:et:
