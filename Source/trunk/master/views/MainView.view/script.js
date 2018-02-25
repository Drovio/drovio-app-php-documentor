var jq = jQuery.noConflict();
jq(document).one("ready", function() {
	// Documentor listener
	jq(document).on("click", ".phpDocumentorWrapper .navitem.documentor", function() {
		jq("#docEditorContainer").trigger("reload");
	});
	
	// Clear editor container
	jq(document).on("editorContainer.clear", function() {
		jq(".codeEditorContainer").html("");
	});
});