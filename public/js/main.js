$(function(){
    $("#form-total").steps({
        headerTag: "div.steps",
        bodyTag: "section",
        transitionEffect: "fade",
        enableAllSteps: true,
        stepsOrientation: "vertical",
        autoFocus: true,
        transitionEffectSpeed: 500,
        titleTemplate : '<div class="title">#title#</div>',
        labels: {
            previous : 'Back Step',
            next : 'Next',
            finish : 'Ready To Process',
            current : ''
        },
    })
});
