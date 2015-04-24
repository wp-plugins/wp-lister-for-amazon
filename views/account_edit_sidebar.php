<style type="text/css">

	#side-sortables .postbox input.text_input,
	#side-sortables .postbox select.select {
	    width: 45%;
	}
	#side-sortables .postbox label.text_label {
	    width: 50%;
	}

	#side-sortables .postbox .inside p.desc {
		margin-left: 2%;
	}

</style>




					<!-- first sidebox -->
					<div class="postbox" id="submitdiv">
						<!--<div title="Click to toggle" class="handlediv"><br></div>-->
						<h3><span><?php echo __('Update','wpla'); ?></span></h3>
						<div class="inside">

							<div id="submitpost" class="submitbox">

								<div id="misc-publishing-actions">
									<div class="misc-pub-section">
										<p>
											Please don't change any account details except for the account title.
										</p>
									</div>
								</div>

								<div id="major-publishing-actions">
									<div id="publishing-action">
										<input type="hidden" name="action" value="save_account" />
										<input type="hidden" name="wpla_account_id" value="<?php echo $wpl_account->id; ?>" />
										<input type="hidden" name="return_to" value="<?php echo @$_GET['return_to']; ?>" />
										<input type="submit" value="<?php echo __('Update','wpla'); ?>" id="publish" class="button-primary" name="save">
									</div>
									<div class="clear"></div>
								</div>

							</div>

						</div>
					</div>

					<!--
					<div class="postbox" id="HelpBox">
						<h3><span><?php echo __('Help','wpla'); ?></span></h3>
						<div class="inside">
							<p>
								Please don't change any account details other than the account title.
							</p>
						</div>
					</div>
					-->

