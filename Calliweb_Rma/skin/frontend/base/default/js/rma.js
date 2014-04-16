/**
 * @category   Calliweb
 * @package    Calliweb_Rma
 * @author	   Calliweb <office@calliweb.fr>
 * @copyright  Copyright (c) 2014 Calliweb (http://www.calliweb.fr)
 * @license    http://creativecommons.org/publicdomain/zero/1.0/ Creative Commons CC0
 */

var Rma =
{
	'orderId': 0,
	'attachmentId': 0,
	'attachment': null
};

Rma.setup = function()
{
	var order_select = $('rma_order');
	if(order_select) {
		order_select.observe('change', Rma.orderChange);
		Rma.orderChange();
	}

	var attachments = $('rma_attachments');
	if(attachments) {
		var add_attachment = $('rma_add_attachment');
		if(add_attachment) add_attachment.observe('click', Rma.addAttachment);
		var remove_attachment = attachments.getElementsByTagName('a')[0];
		if(remove_attachment) remove_attachment.observe('click', Rma.removeAttachment);
		Rma.attachment = attachments.firstDescendant().clone(true);
	}
}

Rma.orderChange = function()
{
	var el = $('order_'+Rma.orderId);
	if(el) el.addClassName('hidden');
	Rma.orderId = $('rma_order').value;
	el = $('order_'+Rma.orderId);
	if(el) el.removeClassName('hidden');
}

Rma.addAttachment = function()
{
	var attachments = $('rma_attachments');
	Rma.attachmentId ++;
	var attachment = Rma.attachment.clone(true);
	var label = attachment.firstDescendant();
	label.writeAttribute('for', label.readAttribute('for')+'_'+Rma.attachmentId); 
	var input = attachment.getElementsByTagName('input')[0];
	input.writeAttribute('id', input.readAttribute('id')+'_'+Rma.attachmentId);
	var remove_attachment = attachment.getElementsByTagName('a')[0];
	remove_attachment.observe('click', Rma.removeAttachment);
	attachments.appendChild(attachment);
}

Rma.removeAttachment = function(e)
{
	e.target.parentNode.remove();
	if($('rma_attachments').childElements().length == 0) Rma.addAttachment();
	Event.stop(e);
}

Validation.addAllThese([
	['validate-number-range', 'The value is not within the specified range.', function(v, elm) {
		var result = Validation.get('IsEmpty').test(v) || (!isNaN(parseNumber(v)) && !/^\s+$/.test(parseNumber(v)));
		var reRange = new RegExp(/^number-range-[0-9]+-[0-9]+$/);
		$w(elm.className).each(function(name, index) {
			if (name.match(reRange) && result) {
				var min = parseInt(name.split('-')[2], 10);
				var max = parseInt(name.split('-')[3], 10);
				var val = parseInt(v, 10);
				result = (v >= min) && (v <= max);
			}
		});
		return result;
	}]
]);

Event.observe(window, 'load', Rma.setup);