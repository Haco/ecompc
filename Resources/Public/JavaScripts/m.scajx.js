function addAjaxLoader(e){$("#"+e).addClass("ajaxloader")}function removeAjaxLoader(e){$("#"+e).removeClass("ajaxloader")}function txEcompcSetOption(){$(".ecom-configurator-select-package-option-wrap").on("click",function(e){return e.preventDefault(),$(this).hasClass("disabled")?void $(this).blur():(addAjaxLoader("ecom-configurator-ajax-loader"),void genericAjaxRequest(t3pid,t3lang,1407764087,"setOption",{option:$(this).attr("data-option"),unset:$(this).attr("data-option-state"),cObj:t3cobj},function(e){var o=$("#ecom-configurator-optionSelection-package-info"),t=$("#ecom-configurator-result-canvas");if(removeAjaxLoader("ecom-configurator-ajax-loader"),updateProgressIndicators(e.progress),e.currentPackage instanceof Object){var a=e.currentPackage.dependencyNotesFluidParsedMessage?"<p>"+e.currentPackage.dependencyNotesFluidParsedMessage+"</p>":"";o.html("<h2>"+e.currentPackage.frontendLabel+"</h2><p>"+e.currentPackage.hintText+"</p>"+a).show()}e.showResult?(o.hide(),$("#ecom-configurator-request").attr("href",e.requestLink),$("#ecom-configurator-result-canvas .ecom-configurator-result h3.ecom-configurator-result-label").first().html(e.configurationData[0]),$("#ecom-configurator-result-canvas .ecom-configurator-result small.ecom-configurator-result-code").first().html(e.configurationData[1]),$("#ecom-configurator-summary-table").html(getConfigurationSummary(e.configurationData[2],e.pricingEnabled)),$(".ecompc-syntax-help").tooltip({tooltipClass:"ecompc-custom-tooltip-styling",track:!0}),t.show(),$("#ecom-configurator-show-result-button").hide(),txEcompcIndex()):(o.show(),t.hide(),1===e.progress?$("#ecom-configurator-show-result-button").show():$("#ecom-configurator-show-result-button").hide()),updatePackageNavigation(e.packages),buildSelector(e),e.pricingEnabled&&e.pricing&&$("#ecom-configurator-config-header-config-price").html(e.pricing)}))})}function txEcompcIndex(){$(".ecom-configurator-package-select").on("click",function(e){return e.preventDefault(),$(this).hasClass("ecom-configurator-package-state-0")||$(this).hasClass("current")?!1:(addAjaxLoader("ecom-configurator-ajax-loader"),void genericAjaxRequest(t3pid,t3lang,1407764087,"index",{"package":$(this).attr("data-package"),cObj:t3cobj},function(e){var o=$("#ecom-configurator-optionSelection-package-info"),t=$("#ecom-configurator-result-canvas");if(removeAjaxLoader("ecom-configurator-ajax-loader"),e.currentPackage instanceof Object){var a=e.currentPackage.dependencyNotesFluidParsedMessage.length?"<p>"+e.currentPackage.dependencyNotesFluidParsedMessage+"</p>":"";o.html("<h2>"+e.currentPackage.frontendLabel+"</h2><p>"+e.currentPackage.hintText+"</p>"+a).show()}e.showResult?(o.hide(),t.show(),$("#ecom-configurator-show-result-button").hide()):(o.show(),t.hide(),1===e.progress?$("#ecom-configurator-show-result-button").show():$("#ecom-configurator-show-result-button").hide()),updatePackageNavigation(e.packages),buildSelector(e)}))})}function getOptionHint(e,o,t,a){var r=$("#ecom-configurator-select-package-option-info-hint-box"),n=$("#ecom-configurator-select-package-option-info-hint-box > div");return r.addClass("ajaxloader"),n.html(""),genericAjaxRequest(o,t,1407764087,"getOptionHint",{option:e,cObj:a},function(e){n.html(e.hint),r.removeClass("ajaxloader")}),!1}function genericAjaxRequest(e,o,t,a,r,n){$.ajax({async:"true",url:"index.php",type:"POST",dataType:"json",data:{eID:"EcomProductConfigurator",id:parseInt(e),L:parseInt(o),type:parseInt(t),request:{controllerName:"SkuConfiguratorAjaxRequest",actionName:a,arguments:r}},success:n,error:function(e,o,t){console.log("Request failed with "+o+": "+t+"!")}})}function updateProgressIndicators(e){$("#ecom-configurator-progress-value").animate({value:e}),$(".ecom-configurator-progress-value-print").each(function(o,t){$({countNum:$(t).text()}).animate({countNum:Math.floor(100*e)},{duration:800,easing:"linear",step:function(){$(t).text(Math.floor(this.countNum))},complete:function(){$(t).text(this.countNum)}})})}function updatePackageNavigation(e){for(var o in e)if(e.hasOwnProperty(o)){if(!e[o].visibleInFrontend)continue;var t=e[o].active?1:0,a=e[o].active?0:1,r=e[o].anyOptionActive,n=$("#ecom-configurator-package-"+e[o].uid+"-link i"),c=$("#ecom-configurator-package-"+e[o].uid+"-link"),i=$("#ecom-configurator-package-"+e[o].uid+"-icon");n.addClass("fa-"+(r?"check-":"")+"square-o").removeClass("fa-"+(r?"":"check-")+"square-o"),c.addClass("ecom-configurator-package-state-"+t).removeClass("ecom-configurator-package-state-"+a),i.addClass("icon-state-"+t).removeClass("icon-state-"+a),e[o].current?(c.addClass("current"),i.addClass("current")):(c.removeClass("current"),i.removeClass("current"))}}function buildSelector(e){var o,t=e.options,a=[],r=1;if(null!==t&&t.length){for(o in t)if(t.hasOwnProperty(o)){var n='<a data-option="'+t[o].uid+'" data-option-state="'+(t[o].active?1:0)+'" class="ecom-configurator-select-package-option-wrap '+(t[o].disabled?"disabled":"enabled")+'" tabindex="'+r+'">';e.pricingEnabled&&(n+='<span class="ecom-configurator-select-package-option-price">'+t[o].price+"</span>"),t[o].hint&&(n+='<div class="ecom-configurator-select-package-option-info-wrapper"><span class="ecom-configurator-select-package-option-info">'+moreInfoLinkTitle+"</span></div>"),n+='<div class="ecom-configurator-checkbox '+(t[o].active?"":"un")+'checked"><span class="ecom-configurator-option-checkbox-image"></span></div>',n+='<span class="ecom-configurator-select-package-option option">'+t[o].title+"</span></a>",n+='<div class="clearfix"></div>',a.push(n),r++}$("#ecom-configurator-select-options-ajax-update").html(a.join("")).show(),$("#ecom-configurator-reset-configuration-button").show(),txEcompcSetOption(),addInfoTrigger()}else e.showResult&&($("#ecom-configurator-select-options-ajax-update").html("").hide(),$("#ecom-configurator-reset-configuration-button").hide())}function getConfigurationSummary(e,o){for(var t="<table>",a=0,r=0;r<e.length;r++)a++,e[r].pkg&&(t+="<tr><td>"+a+"</td><td>"+e[r].pkg+"</td><td>"+e[r][0].replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g,"$1<br />$2")+(""!=e[r][1]?" ["+e[r][1]+"]":"")+"</td><td>"+(e[r].pkgUid?'<a data-package="'+e[r].pkgUid+'" class="ecom-configurator-package-select"><i class="fa fa-edit"></i></a>':"")+"</td>",o&&(t+='<td class="align-right">'+(e[r].pricing?e[r].pricing:"")+"</td>"),t+="</tr>");return t+="</table>"}function addInfoTrigger(){var e,o,t,a,r="#ecom-configurator-canvas .ecom-configurator-select-package-option-info",n="#ecom-configurator-canvas #ecom-configurator-select-package-option-info-hint-box";$(r).on("click",function(r){return r.preventDefault(),getOptionHint($(this).parents("a").first().attr("data-option"),t3pid,t3lang,t3cobj),e=$(document).height(),a=$(n),o=$(a).outerHeight(),t=-5,a.slideDown(),!1})}!function(){txEcompcSetOption(),txEcompcIndex(),addInfoTrigger()}(jQuery);