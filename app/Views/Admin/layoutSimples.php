<?php
echo view('Admin/partialviews/header');
echo view('Admin/'.($camada1 ? $camada1.'/' : '').$camada2.'/'.$pagina.'_view');
echo view('Admin/partialviews/footer');
