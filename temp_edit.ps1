$content = Get-Content 'app\Views\Admin\pages\cadastros\formProcessosDocumentos_view.php' -Raw
$old = '<div class="form-group col-md-3">\r\n\t\t\t\t\t\t\t\t\t\t<label for="nm_versao_vigente">'
$new = '<div class="form-group col-md-3" id="grupoVersaoVigente">\r\n\t\t\t\t\t\t\t\t\t\t<label for="nm_versao_vigente">'
$content = $content -replace [regex]::Escape($old), $new
Set-Content 'app\Views\Admin\pages\cadastros\formProcessosDocumentos_view.php' $content -NoNewline
