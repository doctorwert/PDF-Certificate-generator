$(document).ready(function(){
    console.log('start');
    $('#date').datepicker({
        format: "dd.mm.yyyy",
        maxViewMode: 2,
        todayBtn: "linked",
        clearBtn: true,
        language: "uk",
        multidate: false,
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true,
        toggleActive: true,
        endDate: '0d',
    });
});