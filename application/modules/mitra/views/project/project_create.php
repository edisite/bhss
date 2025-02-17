	<div class="row">
		<div class="col-md-12">
	        <div class="card">
	            <div class="card-header">
	                <h4 class="card-title" id="horz-layout-icons">Form Buat Project</h4>
	                <a class="heading-elements-toggle"><i class="fa fa-ellipsis-v font-medium-3"></i></a>
        			<div class="heading-elements">
	                    <ul class="list-inline mb-0">
	                        <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
	                        <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
	                        <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
	                        <li><a data-action="close"><i class="ft-x"></i></a></li>
	                    </ul>
	                </div>
	            </div>
	            <div class="card-content collpase show">
	                <div class="card-body">    
	                    <form id="formAdd" class="form-horizontal">
	                    	<div class="form-body">
                                        <input type="hidden" id="url" value="mitra/project/postdata">
                                        <input type="hidden" id="url_data" value="mitra/project">
                                        <div id="success"> </div>                                      
                                        <div></div>
                                        <div class="form-group row">
                                                <label class="col-md-4 label-control" for="timesheetinput1">Nama Project</label>
                                                <div class="col-md-8">			                            
                                                    <input type="text" class="form-control" name="project_nama" placeholder="" required data-validation-required-message="Nama Project Wajib di isi" />                                                        
												</div>
										</div>
                                        <div class="form-group row">
                                                <label class="col-md-4 label-control" for="timesheetinput1">Detail Project</label>
                                                <div class="col-md-8">			                            
                                                    <textarea id="projectinput8" rows="5" class="form-control" name="project_detail" placeholder=""></textarea>
											</div>
						</div>
                                </div>

	                        <div class="form-actions right">
									<a href="project">
										<button type="button" class="btn btn-warning mr-1" onclick="reset()">
											<i class="ft-x"></i> Cancel
										</button>
									</a>
                                    <button type="submit" class="btn btn-primary" id="">
									
	                                <i class="fa fa-check-square-o"></i> Save
	                            </button>
	                        </div>
	                    </form>

	                </div>
	            </div>
	        </div>
	    </div>
	</div>
