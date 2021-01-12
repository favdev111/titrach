<?php
require_once(APP_PATH.'extensions/simple_html_dom/simple_html_dom.php');

Class PDF{
    static $base_url = null;
    
	static $pageMargin = array();
	
    static function  render($fn,$html_content,$opt = array()){
        $def = array(
            'layout'=>'Letter',
            'outputMode'=>'I',
			'orientation'=>'P');
		$opt = array_merge($def,$opt);
		if(isset($opt['customPageSize'])){
			$pdf = Yii::createComponent(
				'application.extensions.tcpdf.ETcPdf', 
				$opt['orientation'],
				'cm', 
				$opt['customPageSize'], 
				true, 
				'UTF-8'
			);	
		} else {
			$pdf = Yii::createComponent('application.extensions.tcpdf.ETcPdf', 
                                     $opt['orientation'], 'cm', $opt['layout'], true, 'UTF-8');
		}
        
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(Yii::app()->name);
        $pdf->SetTitle(Yii::app()->name);
        $pdf->SetSubject(Yii::app()->name);
        $pdf->SetKeywords(Yii::app()->name);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetFont("times", "", 12);
        $tagvs = array('p' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)),'div' => array(0 => array('h' => 0, 'n' => 0), 1 => array('h' => 0, 'n' => 0)));
        $pdf->setHtmlVSpace($tagvs);
        $pdf->SetCellPadding(0);
        $pdf->setJPEGQuality(100);
		
		if(count(self::$pageMargin)>0)
		{
			foreach(self::$pageMargin as $func=>$val)
			{
				$pdf->{$func}($val);
			}
		}
		
		
		$blocks = explode('<!-- split -->',$html_content);
		foreach($blocks as $block)
		{
			$html = new simple_html_dom();
			$html->load($block);
			$html2 = new simple_html_dom();
			$trs = $html->find('tr.main');
			if(count($trs)>0){
				$pdf->setAutoPageBreak(false);
				$pdf->startTransaction();
				$no_border = $html2->load($block)->find('table.no-border');
				if(count($no_border)>0)
					$table_tag = '<table border="0" cellpadding="0" width="100%">';
				else
					$table_tag = '<table border="1" cellpadding="5" width="100%">';
				$html_buffer = $table_tag;
				foreach($trs as $tr)
				{
					//var_dump($tr->outertext);
					$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html_buffer.$tr->outertext.'</table>', $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);					
					if ($pdf->getY() < ($pdf->getPageHeight() - 1))
					{
						$html_buffer .= $tr->outertext;
						$pdf->rollbackTransaction(true);
						$pdf->startTransaction();
					}else{
						$pdf->rollbackTransaction(true);
						$pdf->startTransaction();
						if($html_buffer !=$table_tag){
							$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html_buffer.'</table>', $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);											
						}
						$pdf->AddPage();
						$pdf->commitTransaction();
						$pdf->startTransaction();
						$html_buffer = $table_tag.$tr->outertext;
					}
				}
				if($html_buffer !=$table_tag)
					$pdf->writeHTMLCell($w=0, $h=0, $x='', $y='', $html_buffer.'</table>', $border=0, $ln=1, $fill=0, $reseth=true, $align='', $autopadding=false);											
				$pdf->commitTransaction(); 
					
		
			}else{
				$pdf->setAutoPageBreak(true);
				$pdf->writeHTML($block, true, false, true, false, '');
			}
		}
		//die();
		$pdf->Output($fn, $opt['outputMode']);        
    }
    
    static function renderCeckboxGrid($field,$selected,$cols,$rows = null,$return = true){
	    if(!self::$base_url) self::$base_url = Yii::app()->theme->getBaseUrl(true);
        $html = '<table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr><td>';
        $total = count($field->fieldsValues);
        $el_per_col =  $total > $cols  ?  ceil($total/$cols) : 1;
        $i = 0;
        $c = 1;
        foreach($field->fieldsValues as $fvl){
            $i++;
            if($fvl->form_field_value == FieldsValues::SELECT_VALUE_TYPE_BEGING_OF_GROUP)
			{
				$html .='<b>'.$fvl->form_field_title.'</b><br>';
			}else{
				$html .= '<img src="'.self::$base_url.'/img/checkbox_'.(array_key_exists($fvl->id,(array)$selected) ? 'yes':'no'). '_11x11.jpg" width="11" height="11"/> '.$fvl->form_field_title .'<br/>';
            }
			if($i ==$el_per_col && $c!=$cols){
                $i =0;
                $html .= '</td><td>';
                $c++;
            }
        }
        $html .='</td></tr></table>';
        if(!$return){
            echo $html;
        }else
            return $html;
    }
}
?>