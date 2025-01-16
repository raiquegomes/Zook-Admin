$(document).ready(function(){
    $('#cnpj-input').mask('000.000.000-00', {reverse: true});
    $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
  });

  window.printReport = function () {
        const printContents = document.getElementById('printable-area').innerHTML;
        const originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
    };
