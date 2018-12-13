<!-- START BREADCRUMB -->
<ul class="breadcrumb push-down-0">
    <li><a href="javascript:void(0);">CMS Menu</a></li>                    
    <li class="active">Error Page</li>
</ul>
<!-- END BREADCRUMB -->
<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">
    <div class="row">
        <div class="col-md-12">
            <div class="error-container">
                <div class="error-code">404</div>
                <div class="error-text">page not found</div>
                <div class="error-subtext">Unfortunately we're having trouble loading the page you are looking for. Please wait a moment and try again or use action below.</div>
                <div class="error-actions">                                
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-info btn-block btn-lg" onClick="document.location.href = '<?php echo $settings['cms_url']?>';">Back to dashboard</button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-primary btn-block btn-lg" onClick="history.back();">Previous page</button>
                        </div>
                    </div>                                
                </div>
            </div>
        </div>
    </div>                                                
</div>                
<!-- END PAGE CONTENT WRAPPER -->
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
?>
