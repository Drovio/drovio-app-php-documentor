var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Documentor listener
	jq(document).on("click", ".phpDocumentorWrapper .navitem.documentor", function() {
		jq("#docEditorContainer").trigger("reload");
	});
});