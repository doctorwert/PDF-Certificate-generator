<?php

class PDF 
{    
    protected static $template   = 'assets/images/certificate-template.jpg';
    protected static $QRcodeLink = 'https://sreda.wert.com.ua/certificate/{number}';

    public static function certificateGenerateAndDownload( Certificate $cert ) : void
    {
       $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'ISO-8859-1', false);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('WerT');
        $pdf->SetTitle('Certificate Example | PDF file using TCPDF');

        // config
        $pdf->SetFont('dejavusans', '', 12, '', true);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);
        $pdf->setPrintFooter(false);
        $pdf->SetAutoPageBreak(FALSE, 0);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        $pdf->AddPage();
        
        // template
        $pdf->Image( self::$template, 0, 0, 297, 210, '', '', '', false, 300, '', false, false, 0 );
        
        // data
        $pdf->writeHTMLCell(0, 0,  0,  80, '<div style="font-size: 32px; font-weight: bold; text-align: center; color: #104d84;">'.$cert->name.'</div>');
        $pdf->writeHTMLCell(0, 0,  0, 100, '<div style="font-size: 18px; font-weight: bold; text-align: center; color: #222;">'. $cert->curs .'</div>');
        $pdf->writeHTMLCell(0, 0, 65, 174, '<div style="font-size: 16px;">'.((date_create_from_format('Y-m-d', $cert->date))->format('d.m.Y')).'</div>');
        $pdf->writeHTMLCell(0, 0,  0, 195, '<div style="font-size: 10px; text-align:center;">'.$cert->number.'</div>');
        
        // QRcode H (best error correction)
        $link = str_replace('{number}', $cert->number, self::$QRcodeLink);
        $style = [
            'border'  => 2,
            'padding' => 'auto',
            'fgcolor' => array(0,0,255),
            'bgcolor' => array(255,255,64)
        ];
        $pdf->write2DBarcode($link, 'QRCODE,H', 130, 160, 30, 30, $style, 'N');
        
        // download
        $pdf->Output('certificate.pdf', 'I');
    }
}
