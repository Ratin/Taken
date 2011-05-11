//Start----------------MsLinks
$wgStatusList = array("status 1","status 2","status 3","status 4");
require_once("$IP/extensions/MsLinks/mslinks.php");
$wgFileTypes = array("no" => "no_icon.gif",
                      "jpg" => "image_icon.png", 
                       "gif" => "image_icon.png", 
                       "bmp" => "image_icon.png", 
                       "png" => "image_icon.png", 
                       "pps" => "pps_icon.png", 
                       "ppt" => "pps_icon.png", 
                       "pdf" => "pdf_icon.png", 
                       "xls" => "xls_icon.png", 
                       "doc" => "doc_icon.png", 
                       "exe" => "exe_icon.gif",
                       "txt" => "txt_icon.png",
                       "asc" => "txt_icon.png",
                       "docx" => "doc_icon.png",
                       "xlsx" => "xls_icon.png",
                       "dwg" => "dwg_icon.gif",
                       "zip" => "zip_icon.png",
                       "dot" => "doc_icon.png"
                    );
//Ende----------------MsLinks








Datenbank muss zuerst erstellt werden (im javascript)
{{#l:status|Dateiname.ext}}
