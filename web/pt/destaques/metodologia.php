<?php 
    ob_start(); 
?>

<div class="" style="width: 100%; height: 1000px;">
    <div class="" style="background: #0065A5; color: #FFFFFF; font-size: 15pt; font-weight: bold; font-family: helvetica; padding: 12px 0px 12px 5px;">
        METODOLOGIA
    </div>
    <div class="block-01-div-subtitle_destaques">IDHM HOJE</div>
    <img src="img/destaques/metodologia1.png" style="width: 57%; float: right; margin-right: 94px;">
    <div id="columnRight" style="margin-right: 34px; margin-top: 0px;">
        <div id="column" style="margin-right: 15px; text-align: center;">
            <span><b>Vida longa e saudável</b></span><br />
            Esperança de vida ao nascer
        </div>
        <div id="column" style="margin-right: 15px; text-align: center;">
            <span><b>Acesso ao conhecimento</b></span><br />
            % 18+ c/ fundamental completo<br />
            % 5-6 na escola<br />
            % 11-13 anos finais do fundamental<br />
            % 15-17 c/ fundamental completo<br />
            % 18-20 com médio completo<br />
        </div>
        <div id="column" style="text-align: center;">
            <span><b>Padrão de vida</b></span><br />
            Renda mensal per capita (em R$ ago/2010)
        </div>
    </div>
    <div class="clear"></div>
    <div class="">
        <table style="font-size: 10px; text-align: center; font-family: helvetica; float: left;">
            <tr align="center" style="border-bottom: 3px solid #FFFFFF; ">
                <td rowspan="2" style="background: #FFFFFF; width: 67px;"></td>
                <td rowspan="2" style="background: #E992B9; border-right: 3px solid #FFFFFF;"><b>LONGEVIDADE</b></td>
                <td colspan="2" style="background: #FADD7B; border-right: 3px solid #FFFFFF;"><b>EDUCAÇÃO</b></td>
                <td rowspan="2" style="background: #97C657"><b>RENDA</b></td>
	</tr>
	<tr align="center" style="border-bottom: 3px solid #FFFFFF;">
                <td style="background: #FADD7B; border-right: 3px solid #FFFFFF;"><b>População Adulta</b></td>
                <td style="background: #FADD7B"><b>População Jovem</b></td>
	</tr>
            <tr style="border-bottom: 4px solid #000000">
                <td style="font-weight: bold; ">IDHM Brasil 2013</td>
                <td style="background: #F8DBE8; width: 100px; border-right: 3px solid #FFFFFF;">Esperança de Vida ao nascer</td>
                <td style="background: #F7EBC3; width: 143px; border-right: 3px solid #FFFFFF;">18+ com fundamental completo (peso 1)</td>
                <td style="background: #F7EBC3; width: 135px; border-right: 3px solid #FFFFFF;">
                    % 5-6 na escola<br />
                    % 11-13 anos finais do fundamental<br />
                    % 15-17 c/ fundamental completo<br />
                    % 18-20 com médio completo<br />
                </td>
                <td style="background: #D2EBAF">Renda mensal <i>per capita</i> (em R$ ago/2010)</td>
            </tr>
            <tr style="border-bottom: 3px solid #FFFFFF; ">
                <td style="font-weight: bold;">IDHM Global</td>
                <td style="background: #F8DBE8; border-right: 3px solid #FFFFFF;">Esperança de Vida ao nascer</td>
                <td style="background: #F7EBC3; border-right: 3px solid #FFFFFF;">Média de anos de estudo de 25+</td>
                <td style="background: #F7EBC3; border-right: 3px solid #FFFFFF;">Anos Esperado de Estudos</td>
                <td style="background: #D2EBAF">Renda Média Nacional <i>per capita</i> (US$ ppp2005)</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">IDHM 2003</td>
                <td style="background: #F8DBE8; border-right: 3px solid #FFFFFF;">Esperança de Vida ao nascer</td>
                <td style="background: #F7EBC3; border-right: 3px solid #FFFFFF;">Taxa de alfabetização 15+ (Peso 2)</td>
                <td style="background: #F7EBC3; border-right: 3px solid #FFFFFF;">Taxa bruta de frequência à escola (Peso 1)</td>
                <td style="background: #D2EBAF">Renda mensal <i>per capita</i> (em R$ ago/2010)</td>
            </tr>
        </table>
    </div>
</div>

<?php 
    $title = 'Árvore do IDHM';
    $meta_title = 'Árvore do Índice de Desenvolvimento Humano no Brasil 2013';
    $meta_description = 'Visualize os Indicadores Socieconômicos do Brasil no formato da árvore de IDHM';
    $content = ob_get_contents();
    ob_end_clean();
    include "/../../base.php";
?>