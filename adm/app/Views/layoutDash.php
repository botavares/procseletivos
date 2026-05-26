<?php
echo view('partialviews/header');
echo view('partialviews/head');
echo view('partialviews/sidemenu');
echo view($camada1.'/'.$camada2.'/'.$pagina.'_view');
echo view('partialviews/footer');
