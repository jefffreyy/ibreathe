<script type="module">

            import {

                Uppy,

                Dashboard,

                XHRUpload,

                Compressor

            // } from 'https://releases.transloadit.com/uppy/v3.18.0/uppy.min.mjs'  
            // } from "<?= base_url('assets_system/js') ?>/uppy.min.mjs"  
        } from "<?= base_url('assets_system/js') ?>/uppy.min.mjs";

            $(document).ready(function() {
                (function ($) {
                    load_selected_file();
                    const imageGrid         = $("#image-grid");

                    let selectedCard        = null;

                    let parentBrowse        = null;

                    let selectedFileData    = '';

                    let selectedFileArr     = [];

                    let rawFileData         = '';

                    let uppy                = null;

                    let fileLocationData    = 'assets_system/uploads/all';
                    let modalType           = '';

                    let isMultipleSelect    = false;

                    // $('.file_uploader').append(browse_elem);

                    $(document).on("click",".file_uploader", function(){
                        $('.uploader_modal').modal('toggle');
                         parentBrowse=$(this);
                        file_location($(this))
                        // if(uploader_type==='profile_image'){

                        //     fileLocationData='assets_user/user_profile'

                        // }

                        

                    })

                    

                    // $(document).on('show.bs.modal','.uploader_modal', function (event) {

                    //     $('input.uploader-search_file').val('');

                    //     get_files();

                    // })

                    function uppySetup(){

                    let file_type=null;

                    let XHR_ENDPOINT="<?=base_url('uploaders/upload_file')?>";

                    uppy = new Uppy({

                              autoProceed: false, // You can configure auto-upload if needed

                              restrictions: {

                                maxNumberOfFiles: 10, // Define the maximum number of files to upload

                                // allowedFileTypes:file_type,

                              },

                              meta:{

                                  file_location: fileLocationData

                              }

                            })

                        .use(Dashboard, {

                        inline: true,

                        target: "#nav-new_upload",

                        showProgressDetails: true,

                        note: 'Select up to 10 files', 

                        proudlyDisplayPoweredByUppy: false

                        })

                        .use(Compressor)

                        .use(XHRUpload, {

                            endpoint: XHR_ENDPOINT,

                            fieldName: "raw_file",

                            getResponseData: (responseText, response) => {

                              // parse the responseText as JSON or other format

                              const data = JSON.parse(responseText);

                              // return the data object

                              return data;

                            },

                            formData: true,

                            

                        })

                    }  

                    $(document).on('click','button.btn_modal',function(){

                        let url=$(this).attr('url');

                        get_files(url);

                    })

                    $(document).on('click','#nav-new_upload-tab',function(){

                        if (uppy) {

                            uppy.close();

                            uppy = null;

                        }

                        uppySetup()

                    })

                    $(document).on('click','#nav-select_file-tab',function(){

                         get_files();

                    })

                    function get_files(url="<?=base_url('uploaders/get_all_files/0?type=')?>"+ modalType){

                        $.get(url,function(res){

                            let card='';

                            res['data'].forEach(function(element) {

                                if(element.type==='image'){

                                    card += 

                                    `<div data-id='${element.id}' data-file_name='${element.file_name}' data-rawFile='${element.file_original_name+'.'+element.extension}' class="card border card-img p-2 m-2" style="cursor: pointer;width:12rem;height:12rem;flex-grow:1;max-width:12rem">

                                        <img class="card-img-top" style="width:100%;height:8rem;object-fit:cover" src="<?=base_url()?>${element.file_name}" alt="Image ${element.file_original_name}">

                                            <p class="d-flex mt-2" style="font-weight: 500;">

                                            <span class="card-text" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">${element.file_original_name}</span>

                                            <span>.${element.extension}</span>

                                        </p>

                                    </div>`

                                }

                                else if(element.type==='xls'||element.type==='xlsx'){

                                    card += 

                                    `<div data-id='${element.id}' data-file_name='${element.file_name}' data-rawFile='${element.file_original_name+'.'+element.extension}' class="card border card-img p-2 m-2" style="cursor: pointer;width:12rem;height:12rem;">

                                        <img class="card-img-top" style="width:100%;height:8rem;object-fit:cover" src="<?=base_url('assets_system/icons/xls_icon.png')?>" alt="Image ${element.file_original_name}">

                                            <p class="d-flex mt-2" style="font-weight: 500;">

                                            <span class="card-text" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">${element.file_original_name}</span>

                                            <span>.${element.extension}</span>

                                        </p>

                                    </div>`

                                }

                                else if(element.type==='pdf'){

                                    card += 

                                    `<div data-id='${element.id}' data-file_name='${element.file_name}' data-rawFile='${element.file_original_name+'.'+element.extension}' class="card border card-img p-2 m-2" style="cursor: pointer;width:12rem;height:12rem;">

                                        <img class="card-img-top" style="width:100%;height:8rem;object-fit:cover" src="<?=base_url('assets_system/icons/pdf_icon.png')?>" alt="Image ${element.file_original_name}">

                                            <p class="d-flex mt-2" style="font-weight: 500;">

                                            <span class="card-text" style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">${element.file_original_name}</span>

                                            <span>.${element.extension}</span>

                                        </p>

                                    </div>`

                                }

                            });

                            $('button#prev_file').attr('url',res.prev_url);

                            $('button#next_file').attr('url',res.next_url);

                            imageGrid.html(card);

                        },'json')

                    }
                    
                    $(document).on('click','div.card-img',function(){
                        var selectedCards   = $(this);
                        let file            = selectedCards.attr('data-file_name');
                        let rawFile         = selectedCards.attr('data-rawFile');
                        if(!isMultipleSelect){

                            $('div.card-img').removeClass('border-primary');
                            selectedFileArr=[];

                        }
                        $(this).toggleClass('border-primary');
                        let selected_file   = parentBrowse.children('.browse_container').children('.selected_file');

                        selectedFileData=rawFile;
                        if(!selectedFileArr.includes(file)){
                            selectedFileArr.push(file)
                        }
                        

                        rawFileData=rawFile;

                    })

                    $(document).on('change','input.uploader-multi_selection',function(){

                        $('div.card-img').removeClass('border-primary');
                        
                        if($(this).prop('checked')){

                            isMultipleSelect=true;

                            $('button.uploader_file_add').prop('disabled',true);

                           return; 

                        }

                        $('button.uploader_file_add').prop('disabled',false);

                        isMultipleSelect=false;

                    })

                    $(document).on('input','input.uploader-search_file',function(){

                        let value=$(this).val();

                        if(value!=''){

                            get_files("<?=base_url('uploaders/search_file?query=')?>"+value+'&type='+modalType)

                            return;

                        }

                        get_files();

                    })

                    $('button.uploader_file_add').on('click',function(){

                        let input_child=parentBrowse.find('input.selected_images');

                         $('.uploader_modal').modal('toggle');

                        input_child.attr('value',selectedFileData)
                        let selected_file= parentBrowse.children('.browse_container').children('.selected_file');

                        selected_file.html(`<p class="mt-1 p-0" style="font-weight:500">${selectedFileData}</p>`);

                    })
                    $('button.uploader_file_delete').on('click',async function(){
                        console.log(selectedFileArr);
                        let res=await $.post("<?=site_url('uploaders/delete_files')?>",{files:selectedFileArr},function(res){
                            console.log(res)
                            return res;   
                        })
                        get_files();
                        // let input_child=parentBrowse.find('input.selected_images');

                        //  $('.uploader_modal').modal('toggle');

                        //  input_child.attr('value',selectedFileData)
                        // let selected_file= parentBrowse.children('.browse_container').children('.selected_file');

                        // selected_file.html(`<p class="mt-1 p-0" style="font-weight:500">${selectedFileData}</p>`);

                    })
                    
                    $(document).on('click','a.uploader_file_data',function(e){
                        e.stopPropagation()
                    })
                    // load existing or file 
                    function load_selected_file(){
                        let file_uploader = $('.file_uploader')
                        file_uploader.each(async function(index, element){
                            
                            let parentBrowse        = $(element);
                            let input_child         = parentBrowse.find('input.selected_images');
                            let input_child_val     = $(input_child).val();
                            let file_path           = await file_location(element);
                            let is_disabled         = '';
                            if($(element).hasClass('disabled')){
                                is_disabled='d-none'
                            }
                            let browse_elem=`<div class="browse_container">
                                                <div class="input-group ${is_disabled}" style="cursor:pointer" >
                                                  <div class="input-group-prepend ">
                                                    <span class="input-group-text p-0 px-2" >Browse</span>
                                                  </div>
                                                  <div class="form-control p-0 ">
                                                    <span class="input-group-text p-0 rounded-0 h-100 border-0 pl-1" >Choose File</span>
                    
                                                  </div>
                    
                                                </div>
                    
                                                <div class="selected_file">
                                                
                                                </div>
                    
                                            </div>`
                            let browse_elem_valued=`<div class="browse_container">
                                                <div class="input-group ${is_disabled}" style="cursor:pointer" >
                                                  <div class="input-group-prepend ">
                                                    <span class="input-group-text p-0 px-2" >Browse</span>
                                                  </div>
                                                  <div class="form-control p-0 ">
                                                    <span class="input-group-text p-0 rounded-0 h-100 border-0 pl-1" >Choose File</span>
                    
                                                  </div>
                    
                                                </div>
                    
                                                <div class="selected_file">
                                                    <p class="mt-1 p-0" style="font-weight:500">
                                                        <a download class="uploader_file_data" href="<?=base_url()?>${file_path+'/'+input_child_val}"> ${input_child_val}</a>
                                                    </p>
                                                </div>
                    
                                            </div>`
                    
                            if(input_child_val===''||input_child_val===null){
                                $(element).append(browse_elem);
                            }
                            else{
                                $(element).append(browse_elem_valued);
                            }
                            
                        });
                        
                    }
                    
                    async function file_location(elem){
                        let parentBrowse=elem;
                        let uploader_type   = $(parentBrowse).data('type');
                        modalType           = await uploader_type; 
                        get_files();
                        switch(uploader_type){
                            case 'profile_image':
                                fileLocationData='assets_user/user_profile'
                                break;
                            case 'leave':
                                fileLocationData='assets_user/files/leaves'
                                break;

                            case 'offset':

                                fileLocationData='assets_user/files/offsets'

                                break;

                            case 'self_services':

                                fileLocationData='assets_user/files/selfservices';

                                break;
                            case 'hressentials':

                                fileLocationData='assets_user/files/hressentials';

                                break;
                            case 'benefits':

                                fileLocationData='assets_user/files/benefits';

                                break;
                        }
                        return fileLocationData;

                    }
                

                })(jQuery);

                

            });

        </script>