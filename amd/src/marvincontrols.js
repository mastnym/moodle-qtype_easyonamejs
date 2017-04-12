/*
 * @package    qtype_easyonamejs
 * @copyright  2017 Martin Mastny
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module qtype_easyonamejs/marvincontrols
  */
define(['jquery'], function($) {
    var sketcher = null;
    var package = null;
    var webservicesurl = null;
    var answer_regex = /answer\[(\d+)\]/;
    
    var generateImage = function(answerinput){ 
        var answernumber = answer_regex.exec(answerinput.attr("name"))[1];
        if (answerinput.val().trim() !== '') {
            var imgData = package.ImageExporter.molToDataUrl(answerinput.val(), "image/png", sketcher.getDisplaySettings());
            $("#fgroup_id_answeroptions_" + answernumber).find(".marvin-image:first").attr("src", imgData);
            answerinput.trigger('change');
        }
    };
    return {
       /** 
        * Initialize MarvinJS on add/edit question page
        * @access public
        * @return {null}
        */
        initedit: function(params){
            $(document).ready(function(){
                webservicesurl = params.wsurl.endsWith("/") ? params.wsurl : params.wsurl + '/';
                window.MarvinJSUtil.getEditor(params.editorid).then(function (sketcherInstance) {
                    sketcher = sketcherInstance;
                    $(".marvin-overlay").removeClass("marvin-overlay");
                    if (params.defaultsettings){
                        var defaultsettings = window.JSON.parse(params.defaultsettings);
                        sketcher.setDisplaySettings(defaultsettings);
                    }
                    sketcher.importStructure("mol", $("input[name=answer\\[0\\]]").val());
                    //export answer to textarea
                    $("body").on("click", "input.id_insert", function(){
                        var answernumber = $(this).attr("id").replace("id_insert_", "");
                        sketcher.exportStructure("mol").then(function(struct){
                            var answerinput = $("input[name=answer\\["+ answernumber + "\\]]").val(struct).trigger('change');
                            generateImage(answerinput);
//                            if (params.usews && webservicesurl){
//                                $.post(webservicesurl + 'rest-v0/util/calculate/stringMolExport', {
//                                    "structure": struct,
//                                    "parameters": "cxsmiles"
//                                }).done(function(data){
//                                    $("input[name=answer_smiles\\["+answernumber+"\\]]").val(data);
//                                });
//                            }
                        });              
                    });
                    // adjust buttons on molchange
                    sketcher.on("molchange", function(){
                        $("input.mol-answer").trigger("change");
                    });
                    // load answer to editor
                    $("body").on("click", "input.id_view", function(){
                        var answernumber = $(this).attr("id").replace("id_view_", "");
                        var mol = $("input[name=answer\\["+ answernumber + "\\]]").val();
                        sketcher.importStructure("mol", mol);         
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
                        $("#id_marvinsettings").val(window.JSON.stringify(sketcher.getDisplaySettings())).trigger("change");
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
                                sketcher.setDisplaySettings(settingsobj);
                            }catch(err){
                                window.console.log(err);
                            }
                        }
                    });
                    $("#id_marvinsettings").trigger("change");
                    if ($("#id_marvinsettings").val().trim() !== ''){
                        $("#id_marvinsettingsset").trigger("click");
                    }
                    
                });
                // create images from answers and register click
                window.MarvinJSUtil.getPackage(params.editorid).then(function (marvin_package) {
                    package = marvin_package;
                    $(".mol-answer").each(function () {
                        generateImage($(this));
                        $(this).trigger('change');
                    });
                });      
            });     
        },
        initquestion: function(params){
            $(document).ready(function(){
                window.MarvinJSUtil.getEditor(params.editorid).then(function (sketcherInstance) {
                    sketcher = sketcherInstance;
                    $(".marvin-overlay").removeClass("marvin-overlay");
                    if (params.defaultsettings){
                        var defaultsettings = window.JSON.parse(params.defaultsettings);
                        sketcher.setDisplaySettings(defaultsettings);
                    }
                    // listen for molecule change
                    sketcher.on("molchange", function(){
                        if (!sketcher.isEmpty()){   
                            sketcher.exportStructure("mol").then(function(struct){
                                answer_input.val(struct);
                            });
                        }
                    });
                    var answer_input = $(document.getElementById(params.answerinputid));
                    if (answer_input.val().trim() !== ''){
                        sketcher.importStructure('mol', answer_input.val());
                    }
                    
                    $("body").on('click', 'input[name$=_showcorrectanswer]', function(){
                        var qnum = $(this).attr("name").replace("_showcorrectanswer", '');
                        var qnumselector = qnum.replace(":","\\:")
                        var correct_answer = $("input[name="+qnumselector+"_correctanswer]");
                        var answer = $("input[name="+qnumselector+"_currentanswer]");
                        var myanswerlabel = $(this).data("label-my");
                        var correctanswerlabel = $(this).data("label-correct");
                        // showcorrect answer
                        if ($(this).val() === correctanswerlabel){
                            sketcher.importStructure('mol', correct_answer.val())
                            $(this).val(myanswerlabel);
                        }else{
                            sketcher.importStructure('mol', answer.val());
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
                    
                });
                
                
            });
        }
    };

});

