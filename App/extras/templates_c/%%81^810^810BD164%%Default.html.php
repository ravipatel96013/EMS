<?php /* Smarty version 2.6.31, created on 2022-07-13 10:04:26
         compiled from Default.html */ ?>
<!DOCTYPE html>
<html lang="en-US">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>
      <?php if ($this->_tpl_vars['page_title'] == ""): ?>TinyPHP<?php else: ?>TinyPHP | <?php echo $this->_tpl_vars['page_title']; ?>
 <?php endif; ?>
    </title>
    <meta name="description" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="apple-touch-icon" href="apple-touch-icon.png" />
    <!-- Place favicon.ico in the root directory -->

    <!-- Latest compiled and minified CSS -->
    <script type="text/javascript" src="/assets/js/js.jquery.min.js"></script>
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link
      rel="stylesheet"
      href="/assets/css/fontawesome-free-6.1.1-web/css/all.min.css"
    />

    <link rel="stylesheet" href="/assets/css/adminlte.min.css" />
    <link rel="stylesheet" href="/assets/css/icheck-bootstrap.min.css" />
    <link
      rel="stylesheet"
      href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"
    />

    <?php echo $this->_tpl_vars['styleSheets']; ?>
 <?php echo $this->_tpl_vars['headerScripts']; ?>

  </head>

  <body class="<?php echo $this->_tpl_vars['bodyClasses']; ?>
 layout-top-nav">
    <?php if ($this->_tpl_vars['showHeader']): ?> <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../views/partials/header.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> <?php endif; ?>

    <div class="content-wrapper"><?php echo $this->_tpl_vars['viewContent']; ?>
</div>

    <?php if ($this->_tpl_vars['showFooter']): ?> <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "../views/partials/footer.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?> <?php endif; ?>

   
    <link
      rel="stylesheet"
      type="text/css"
      href="/assets/DataTables/DataTables-1.12.1/css/jquery.dataTables.css"
    />
    <script src="/assets/js/bootstrap.min.js" crossorigin="anonymous"></script>

    <script
      type="text/javascript"
      charset="utf8"
      src="/assets/DataTables/DataTables-1.12.1/js/jquery.dataTables.js"
    ></script>

    <?php echo $this->_tpl_vars['footerScripts']; ?>

  </body>
</html>