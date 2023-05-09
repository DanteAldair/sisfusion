$().ready(function () {
  myFunctions = {
    validateEmptyField(field) {
      if (field == null || field == undefined || field == "") {
        return "N/A";
      } else {
        return field;
      }
    },
    number_format(number, decimals, dec_point, thousands_sep) {
      // Strip all characters but numerical ones.
      number = (number + "").replace(/[^0-9+\-Ee.]/g, "");
      var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = typeof thousands_sep === "undefined" ? "," : thousands_sep,
        dec = typeof dec_point === "undefined" ? "." : dec_point,
        s = "",
        toFixedFix = function (n, prec) {
          var k = Math.pow(10, prec);
          return "" + Math.round(n * k) / k;
        };
      // Fix for IE parseFloat(0.55).toFixed(0) = 0;
      s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
      if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
      }
      if ((s[1] || "").length < prec) {
        s[1] = s[1] || "";
        s[1] += new Array(prec - s[1].length + 1).join("0");
      }
      return s.join(dec);
    },
    muestraAlerta(title, message, type) {
      //tipos: warning, success, error, info
      swal({
        title: title,
        text: message,
        buttonsStyling: false,
        confirmButtonClass: "btn btn-success",
        type: type,
        confirmButtonText: "Si, entiendo",
      });
    },
    validateEmptyFieldDocs(field) {
      if (field == null || field == undefined || field == "") {
        return "";
      } else {
        return field;
      }
    },
    convertDateYMDHMS(inputFormat) {
      //revuelve la fecha sin los microsegundos YYYY-MM-DD HH:MM
      let fecha_cruda = new Date(inputFormat);

      let year = fecha_cruda.getFullYear();
      let month =
        fecha_cruda.getMonth() + 1 < 10
          ? "0" + (fecha_cruda.getMonth() + 1)
          : fecha_cruda.getMonth() + 1;
      let day =
        fecha_cruda.getDate() < 10
          ? "0" + fecha_cruda.getDate()
          : fecha_cruda.getDate();
      let hour =
        fecha_cruda.getHours() < 10
          ? "0" + fecha_cruda.getHours()
          : fecha_cruda.getHours();
      let minutes =
        fecha_cruda.getMinutes() < 10
          ? "0" + fecha_cruda.getMinutes()
          : fecha_cruda.getMinutes();
      let seconds =
        fecha_cruda.getSeconds() < 10
          ? "0" + fecha_cruda.getSeconds()
          : fecha_cruda.getSeconds();

      return (
        year +
        "-" +
        month +
        "-" +
        day +
        " " +
        hour +
        ":" +
        minutes +
        ":" +
        seconds
      );
    },
    revisaObservacionUrgente(parametro) {
      if (parametro == 1) {
        return "disabled";
      } else {
        return "";
      }
    },
  };
});

function onlyNumbers(e) {
  key = e.keyCode || e.which;
  tecla = String.fromCharCode(key);
  letras = " 0123456789";
  especiales = [8, 37, 39, 46];

  tecla_especial = false;
  for (var i in especiales) {
    if (key == especiales[i]) {
      tecla_especial = true;
      break;
    }
  }

  if (letras.indexOf(tecla) == -1 && !tecla_especial) return false;
}

function formatNumber(n) {
  return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// AA: Fn set format money
function formatMoney(n) {
  var c = isNaN((c = Math.abs(c))) ? 2 : c,
    d = d == undefined ? "." : d,
    t = t == undefined ? "," : t,
    s = n < 0 ? "-" : "",
    i = String(parseInt((n = Math.abs(Number(n) || 0).toFixed(c)))),
    j = (j = i.length) > 3 ? j % 3 : 0;
  return (
    s +
    (j ? i.substr(0, j) + t : "") +
    i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) +
    (c
      ? d +
        Math.abs(n - i)
          .toFixed(c)
          .slice(2)
      : "")
  );
}

//AA: Fn fomat thounsand
function formatAsThousands(number) {
  number = number
    .toString()
    .split("")
    .reverse()
    .join("")
    .replace(/(?=\d*\.?)(\d{3})/g, "$1,");
  number = number.split("").reverse().join("").replace(/^[\.]/, "");
  return number;
}

// AA: Fn change full date to format (dd/mm/yyyy)
function convertDate(inputFormat) {
  function pad(s) {
    return s < 10 ? "0" + s : s;
  }
  var d = new Date(inputFormat);
  var date = [pad(d.getDate()), pad(d.getMonth() + 1), d.getFullYear()].join(
    "/"
  );
  var formated = date.replace(/(..)\/(..)\/(....)/, "$2/$1/$3");
  return formated;
}

//AA: Inicializar filtros de fechas por defecto mes corriente
function setIniDatesXMonth(inicioFecha, finFecha) {
  // BEGIN DATE
  const fechaInicio = new Date();
  // Iniciar en este año, este mes, en el día 1
  const beginDate = new Date(
    fechaInicio.getFullYear(),
    fechaInicio.getMonth(),
    1
  );
  // END DATE
  const fechaFin = new Date();
  // Iniciar en este año, el siguiente mes, en el día 0 (así que así nos regresamos un día)
  const endDate = new Date(fechaFin.getFullYear(), fechaFin.getMonth() + 1, 0);
  finalBeginDate = [
    beginDate.getFullYear(),
    ("0" + (beginDate.getMonth() + 1)).slice(-2),
    ("0" + beginDate.getDate()).slice(-2),
  ].join("-");
  finalEndDate = [
    endDate.getFullYear(),
    ("0" + (endDate.getMonth() + 1)).slice(-2),
    ("0" + endDate.getDate()).slice(-2),
  ].join("-");
  finalBeginDate2 = [
    ("0" + beginDate.getDate()).slice(-2),
    ("0" + (beginDate.getMonth() + 1)).slice(-2),
    beginDate.getFullYear(),
  ].join("/");
  finalEndDate2 = [
    ("0" + endDate.getDate()).slice(-2),
    ("0" + (endDate.getMonth() + 1)).slice(-2),
    endDate.getFullYear(),
  ].join("/");

  $("" + inicioFecha + "").val(finalBeginDate);
  $("" + finFecha + "").val(finalEndDate);
}

function remplazarCaracter(text, busca, reemplaza) {
  while (text.toString().indexOf(busca) != -1)
    text = text.toString().replace(busca, reemplaza);
  return text;
}
