/*
 * @package    qtype_easyonamejs
 * @copyright  2017 Martin Mastny
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module qtype_easyonamejs/marvincontrols
  */
define(['jquery','core/notification', 'core/str'], function($, notification, str) {
    var marvinpackage = null;
    var webservicesurl = null;
    var answer_regex = /answer\[(\d+)\]/;
    
    
    var generateImage = function(answerinput, displaysettings){ 
        var answernumber = answer_regex.exec(answerinput.attr("name"))[1];
        if (answerinput.val().trim() !== '') {
            var imgData = marvinpackage.ImageExporter.molToDataUrl(answerinput.val(), "image/png", displaysettings);
            $("#fgroup_id_answeroptions_" + answernumber).find(".marvin-image:first").attr("src", imgData);
            answerinput.trigger('change');
        }
    };
    var failure = function(type, err){ 
        str.get_strings([{key: type + 'failuretitle', component: 'qtype_easyonamejs'},
            {key: type + 'failure', component: 'qtype_easyonamejs'}]).done(
                function (s) {
                    notification.alert(s[0], s[1] + "<br/>" + err, 'Ok');
                }
        ).fail(notification.exception);
    };
    
    
    return {
       /** 
        * Initialize MarvinJS on add/edit question page
        * @access public
        * @return {null}
        */
        initedit: function(params){
            $(document).ready(function(){
                webservicesurl = webservicesurl || params.wsurl.endsWith("/") ? params.wsurl : params.wsurl + '/';
                window.MarvinJSUtil.getEditor(params.editorid).then(function (sketcherInstance) {
                    $(".marvin-overlay").remove();
                    if (params.defaultsettings){
                        var defaultsettings = window.JSON.parse(params.defaultsettings);
                        sketcherInstance.setDisplaySettings(defaultsettings);
                    }
                    sketcherInstance.importStructure("mol", $("input[name=answer\\[0\\]]").val()).then(null, function (err) {
                        failure('import', err);
                    });
                    //export answer to textarea
                    $("body").on("click", "input.id_insert", function(){
                        var answernumber = $(this).attr("id").replace("id_insert_", "");
                        sketcherInstance.exportStructure("mol").then(function(struct){
                            var answerinput = $("input[name=answer\\["+ answernumber + "\\]]").val(struct).trigger('change');
                            generateImage(answerinput, sketcherInstance.getDisplaySettings());
//                            if (params.usews && webservicesurl){
//                                $.post(webservicesurl + 'rest-v0/util/calculate/stringMolExport', {
//                                    "structure": struct,
//                                    "parameters": "cxsmiles"
//                                }).done(function(data){
//                                    $("input[name=answer_smiles\\["+answernumber+"\\]]").val(data);
//                                });
//                            }
                        }, function(err){
                            failure('export', err);
                        });              
                    });
                    // adjust buttons on molchange
                    sketcherInstance.on("molchange", function(){
                        $("input.mol-answer").trigger("change");
                    });
                    // load answer to editor
                    $("body").on("click", "input.id_view", function(){
                        var answernumber = $(this).attr("id").replace("id_view_", "");
                        var mol = $("input[name=answer\\["+ answernumber + "\\]]").val();
                        sketcherInstance.importStructure("mol", mol).then(null, function(err){
                                failure('import', err);
                            });         
                    });
                    // delete answer
                    $("body").on("click", "input.id_delete", function(){
                        var answernumber = $(this).attr("id").replace("id_delete_", "");
                        $("input[name=answer\\["+ answernumber + "\\]]").val("").trigger("change");
                    });
                    // if answer is blank, dont let user import it in editor
                    $("body").on("change", "input.mol-answer", function(){
                       var answernumber = answer_regex.exec($(this).attr("name"))[1]; 
                       if ($(this).val().trim() === ''){
                           $("#id_view_" + answernumber + ", #id_delete_" + answernumber).attr('disabled', true);
                           $("#fgroup_id_answeroptions_" + answernumber).find(".marvin-image:first").attr("src", "");
                       }else{
                           $("#id_view_" + answernumber + ", #id_delete_" + answernumber).attr('disabled', false);
                       } 
                    });
                    // use current editor settings as question settings
                    $("#id_marvinsettingsget").click(function(){
                        $("#id_marvinsettings").val(window.JSON.stringify(sketcherInstance.getDisplaySettings())).trigger("change");
                    });
                    // enable/disable setting options to editor
                    $("body").on("change textInput input", "#id_marvinsettings", function(){
                        if ($(this).val().trim() !== ''){
                            $("#id_marvinsettingsset").attr("disabled", false);
                        }else{
                            $("#id_marvinsettingsset").attr("disabled", true);
                        }
                    });
                    // try to set filled options to editor
                    $("#id_marvinsettingsset").click(function(){
                        var settings = $("#id_marvinsettings").val();
                        if (settings.trim()){
                            try{
                                var settingsobj = window.JSON.parse(settings);
                                sketcherInstance.setDisplaySettings(settingsobj);
                            }catch(err){
                                failure('marvinsettingsimport', err);
                            }
                        }
                    });

                    // create images from answers and register click
                    window.MarvinJSUtil.getPackage(params.editorid).then(function (package) {
                        $(".mol-answer").each(function () {
                            marvinpackage = package;
                            generateImage($(this), sketcherInstance.getDisplaySettings());
                            $(this).trigger('change');
                        });
                    }, function (err) {
                        failure('init', err);
                    });
                    
                    $("#id_marvinsettings").trigger("change");
                    if ($("#id_marvinsettings").val().trim() !== ''){
                        $("#id_marvinsettingsset").trigger("click");
                    }
                    
                }, function(err){
                    failure('init', err);
                });
                    
            });     
        },
        initquestion: function(params){
            var qaid = params.editorid.replace("_marvinjs", "");
            var qaid_selector = qaid.replace(":", "\\:");
            var answer_input = $("#"+qaid_selector+"_answer");
            $(document).ready(function(){
                window.MarvinJSUtil.getEditor(params.editorid).then(function (sketcherInstance) {
                    //remove overlay - editor is loaded
                    $("#"+ qaid_selector + "_applet div.marvin-overlay").remove();
                    if (params.defaultsettings){
                        var defaultsettings = window.JSON.parse(params.defaultsettings);
                        sketcherInstance.setDisplaySettings(defaultsettings);
                    }
                    // import molecule from last attempt if present
                    if (answer_input.val().trim() !== ''){
                        sketcherInstance.importStructure('mol', answer_input.val()).then(null, function (err) {
                            failure('import', err);
                        });
                    }
                    
                    // listen for molecule change
                    sketcherInstance.on("molchange", function(){
                        if (!sketcherInstance.isEmpty()) {
                            sketcherInstance.exportStructure("mol").then(function (struct) {
                                answer_input.val(struct);
                            }, function (err) {
                                failure('export', err);
                            });
                        }
                    });
                               
                    $("body").on('click', 'input[name$='+qaid_selector+'_showcorrectanswer]', function(){
                        var correct_answer = $("input[name="+qaid_selector+"_correctanswer]");
                        var current_answer = $("input[name="+qaid_selector+"_currentanswer]");
                        var myanswerlabel = $(this).data("label-my");
                        var correctanswerlabel = $(this).data("label-correct");
                        // showcorrect answer
                        if ($(this).val() === correctanswerlabel){
                            sketcherInstance.importStructure('mol', correct_answer.val()).then(null, function(err){
                                failure('import', err);
                            });
                            $(this).val(myanswerlabel);
                        }else{
                            sketcherInstance.importStructure('mol', current_answer.val()).then(null, function(err){
                                failure('import', err);
                            });
                            $(this).val(correctanswerlabel);
                        }
                    });
                    
                    var mjsiframe = $("#"+params.editorid);
                    if(mjsiframe.hasClass('correct')){
                        mjsiframe.contents().find(".mjs-canvas").css("background-color", "#dff0d8");
                    }else if(mjsiframe.hasClass('incorrect')){
                        mjsiframe.contents().find(".mjs-canvas").css("background-color", "#f2dede");
                    }else if(mjsiframe.hasClass('partiallycorrect')){
                        mjsiframe.contents().find(".mjs-canvas").css("background-color", "#fcf8e3");
                    }     
                }, function(err){   
                    failure('init', err);
                });    
            });
        },
        initsettings: function(){
            $(document).ready(function(){ 
                $("#id_s_qtype_easyonamejs_usews").change(function(){
                    $("#id_s_qtype_easyonamejs_wsurl").attr("disabled", !this.checked);
                    $("#id_s_qtype_easyonamejs_obabelpath").attr("disabled", this.checked);
                }).trigger("change");
            });
        }
    };

});

