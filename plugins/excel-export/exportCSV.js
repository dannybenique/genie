//convertir json a excel
function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
  var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
  var CSV = '';
  //Set Report title in first row or line
  //CSV += ReportTitle + '\r\n\n';

  //This condition will generate the Label/Header
  if (ShowLabel) {
      var row = "";
      //This loop will extract the label from 1st index of on array
      for (var index in arrData[0]) { row += index + ','; } //Now convert each value to string and comma-seprated
      row = row.slice(0, -1);
      CSV += row + '\r\n'; //append Label row with line break
  }

  //1st loop is to extract each row
  for (var i = 0; i < arrData.length; i++) {
      var row = "";
      for (var index in arrData[i]) { row += '"' + arrData[i][index] + '",'; }
      row.slice(0, row.length - 1);
      CSV += row + '\r\n';
  }

  if (CSV == '') {
      alert("Invalid data");
      return;
  }

  var fileName = ReportTitle.replace(/ /g,"_");
  var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
  var link = document.createElement("a");
  link.href = uri;
  link.style = "visibility:hidden";
  link.download = fileName + ".csv";

  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
