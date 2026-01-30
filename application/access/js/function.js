var contenedor = {};
var json = [];
var json_active = [];
var timeout;
var result = {};
var control = 1;

$(document).ready(function() {

    $('#buscador').keyup(function() {  
        if (timeout) {    
            clearTimeout(timeout);    
            timeout = null;  
        }

          
        timeout = setTimeout(function() {
            search();
        }, 100);
    });


    $("body").on('change', '#result', function() {
        result = $("#result").val();
        load_content(json);
    });

    $("body").on('click', '.asc', function() {
        var name = $(this).parent().attr('rel');
      //  console.log(name);
        $(this).removeClass("asc").addClass("desc");
        order(name, true);
    });

    $("body").on('click', '.desc', function() {
        var name = $(this).parent().attr('rel');
        $(this).removeClass("desc").addClass("asc");
        order(name, false);
    });

});

function update(id,parent,valor){
    for (var i=0; i< json.length; i++) {
        if (json[i].id === id){
            json[i][parent] = valor;
            return;
        }
    }
}

function load_content(json) {

    max = result;
    data = json.slice(0, max);
    json_active = json;
    
    $("#numRows").html(json.length);
    contenedor.html('');
    var list = table.find("th[rel]");
    var html = '';

    $.each(data, function(i, value) {
        html += '<tr id="' + value.id + '">';
        $.each(list, function(index) {

            valor = $(this).attr('rel');

            if (valor != 'acction') {
                if ($(this).hasClass("editable")) {
                    text = value[valor];
                    html += '<td><span class="edit" rel="' + value.id + '">' + text + '</span></td>';
                } else {
                    html += '<td>' + value[valor] + '</td>';
                }

            } else {

                if(control > 0){
                    html += '<td>';
                    $.each(acction, function(k, data) {
                        
                        if(value[data.control]){
                            if(control == value[data.control]){
                                html += '<a class="' + data.class + '" title="'+data.title+'" data-id="' +value[data.dataid]+ '" rel="' +data.rel+ '" href="' + data.link + value[data.parameter] + '"  target="'+data.target+'" >' + data.button + '</a>';
                            }
                        }else{
                            html += '<a class="' + data.class + '" title="'+data.title+'" data-id="' +value[data.dataid]+ '"  rel="' +data.rel+ '" href="' + data.link + value[data.parameter] + '"  target="'+data.target+'" >' + data.button + '</a>';
                        }

                    });
                    html += "</td>"; 
                
                }else{
                    html += '<td>';
                    $.each(acction, function(k, data) {
                        html += '<a class="' + data.class + '" title="'+data.title+'" rel="' + value[data.rel] + '" href="' + data.link + value[data.parameter] + '"  target="'+data.target+'" >' + data.button + '</a>';
                    });
                    html += "</td>"; 
                }

            }

            if (index >= list.length - 1) {
                html += '</tr>';
                contenedor.append(html);
                html = '';
            }
        });
    });

}

function selectedRow(json) {

    var num = result;
    var rows = json.length;
    var total = rows / num;
    var cant = Math.floor(total);
    $("#result").html('');
    for (i = 0; i < cant; i++) {
        $("#result").append("<option value=\"" + parseInt(num) + "\">" + num + "</option>");
        num = num + result;
    }
    $("#result").append("<option value=\"" + parseInt(rows) + "\">" + rows + "</option>");

}

function order(prop,asc) {
    if(prop == 'id'){
        json = json.sort(function(a, b) {
          if (asc) {
            return (parseInt(a[prop]) > parseInt(b[prop])) ? 1 : ((parseInt(a[prop]) < parseInt(b[prop])) ? -1 : 0);
          } else {
            return (parseInt(b[prop]) > parseInt(a[prop])) ? 1 : ((parseInt(b[prop]) < parseInt(a[prop])) ? -1 : 0);
          }
        });
    }else{
        json = json.sort(function(a, b) {
            if (asc) return (a[prop] > b[prop]) ? 1 : ((a[prop] < b[prop]) ? -1 : 0);
            else return (b[prop] > a[prop]) ? 1 : ((b[prop] < a[prop]) ? -1 : 0);
        });
    }
    contenedor.html('');
    load_content(json);   
}


function search() {

    var list = table.find("th[rel]");
    var data = [];
    var serch = $("#buscador").val();

    json.forEach(function(element, index, array) {

        $.each(list, function(index) {
            valor = $(this).attr('rel');

            if (element[valor]) {
                if (element[valor].like('%' + serch + '%')) {
                    data.push(element);
                    return false;
                }
            }

        });

    });

    contenedor.html('');
    load_content(data);

}

String.prototype.like = function(search) {

    if (typeof search !== 'string' || this === null) {
        return false;
    }
    search = search.replace(new RegExp("([\\.\\\\\\+\\*\\?\\[\\^\\]\\$\\(\\)\\{\\}\\=\\!\\<\\>\\|\\:\\-])", "g"), "\\$1");
    search = search.replace(/%/g, '.*').replace(/_/g, '.');
    return RegExp('^' + search + '$', 'gi').test(this);
}


function export_csv(JSONData, ReportTitle, ShowLabel) {
    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
    
    var CSV = '';    
    
    var list = table.find("th[rel]");
    if (ShowLabel) {
        var row = "";

        $.each(list, function() {
            valor = $(this).attr('rel');
            if (valor != 'acction') {
                row +=valor + ';';  
            }
        });

        row = row.slice(0, -1);

        CSV += row + '\r\n';
    }
    
    json.forEach(function(element, index, array) {
        var row = "";
        $.each(list, function(index) {
            valor = $(this).attr('rel');
            if (valor != 'acction') {
                row += '"' + element[valor] + '";';  
            }
        });
        row.slice(0, row.length - 1);
        CSV += row + '\r\n';
    });


    if (CSV == '') {        
        alert("Invalid data");
        return;
    }   
    
    var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
    var link = document.createElement("a");    
    link.href = uri;
    link.style = "visibility:hidden";
    link.download = ReportTitle + ".csv";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#new_photo').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }
}
