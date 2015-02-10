goog.provide('Fewlines');
goog.provide('Fewlines.Application');

/**
 * @constructor
 */
Fewlines.Application = function(){};
goog.addSingletonGetter(Fewlines.Application);

Fewlines.Application.prototype.init = function()
{
	console.log('Fewlines initalized');
};

Fewlines.bootstrap = function()
{
	Fewlines.Application.getInstance().init();	
};

goog.exportSymbol(
    'Fewlines.bootstrap',
    Fewlines.bootstrap
);