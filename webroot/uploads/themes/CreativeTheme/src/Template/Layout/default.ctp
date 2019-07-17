<!-- DOCTYPE -->
<?= $this->Html->docType(); ?>
<html lang="en">
	<?= $this->element('head') ?>
  	<body>
	  	<!--CSRF Token-->
		<input id="csrf-ajax-token" type="hidden" name="token" value=<?= json_encode($this->request->getParam('_csrfToken')); ?>>
  		<!-- Client Timezone -->
		<input id="client-timezone-url" type="hidden" name="clientTimezoneUrl" value="<?= $this->Url->build(['_name' => 'setClientTimezone']); ?>">
		
  		<div id="all">
			<div class="container-fluid">
				<div class="row row-offcanvas row-offcanvas-left">
					<!--   *** SIDEBAR ***-->
					<div id="sidebar" class="col-md-4 col-lg-3 sidebar-offcanvas">
						<div class="sidebar-content">
							<h1 class="sidebar-heading non-uikit">
								<a class="non-uikit <?php if ($logo != '') echo 'theme-main-logo' ?>" href="<?= $this->Url->build(['_name' => 'home']); ?>">
									<?php
						                if ($logo == ''):
						                    echo $siteName;
						                else:
						                	echo '<img src="'.$this->request->getAttribute("webroot").'uploads/images/original/' . $logo.'" alt="'.$siteName.'">';
						                endif; 
						            ?>
								</a>
							</h1>
							<?= $themeFunction->sidebarAbout() ?>

							<?= $this->element('navigation') ?>
							
							<?php
								if ($socials->count() > 0):
							?>
							<p class="social">
								<?php
									foreach ($socials as $social):
								?>
								<a href="<?= $social->link ?>" target="_blank" data-animate-hover="pulse" class="non-uikit external <?= $social->name ?>">
									<i class="fa fa-<?= $social->name ?>"></i>
								</a>
								<?php
									endforeach;
								?>
							</p>
							<?php
								endif;
							?>

							<?= $this->element('footer') ?>
						</div>
					</div>

					<div class="col-md-8 col-lg-9 content-column <?php if ($this->request->getParam('action') != 'home') echo 'white-background' ?>">
			          	<div class="small-navbar d-flex d-md-none">
			            	<button type="button" data-toggle="offcanvas" class="btn btn-outline-primary"> <i class="fa fa-align-left mr-2"></i>Menu</button>
			            	<h1 class="small-navbar-heading non-uikit">
			            		<a class="non-uikit <?php if ($logo != '') echo 'theme-main-logo' ?>" href="<?= $this->Url->build(['_name' => 'home']); ?>">
			            			<?php
						                if ($logo == ''):
						                    echo $siteName;
						                else:
						                    echo '<img src="'.$this->request->getAttribute("webroot").'uploads/images/original/' . $logo.'" alt="'.$siteName.'" width="200">';
						                endif; 
						            ?>
			            		</a>
			            	</h1>
			          	</div>

			          	<!-- Fetch Content -->
						<?= $this->fetch('content') ?>
		            </div>
				</div>
		    </div>
	  	</div>

		<?= $this->element('script') ?>
  	</body>
</html>