// +--------------------------------------------------------------------------+
// | geeklog CMS                                                              |
// +--------------------------------------------------------------------------+
// | submitmailuser_fckeditor.js                                              |
// |                                                                          |
// | Javascript functions for FCKEditor Integration into geeklog              |
// +--------------------------------------------------------------------------+
// | Copyright (C) 2008-2009 by the following authors:                        |
// |                                                                          |
// | Mark R. Evans          mark AT geeklog DOT org                           |
// |                                                                          |
// | Based on the Geeklog CMS                                                 |
// | Copyright (C) 2003-2008 by the following authors:                        |
// |                                                                          |
// | Authors:   Blaine Lang - blaine@portalparts.com                          |
// +--------------------------------------------------------------------------+
// |                                                                          |
// | This program is free software; you can redistribute it and/or            |
// | modify it under the terms of the GNU General Public License              |
// | as published by the Free Software Foundation; either version 2           |
// | of the License, or (at your option) any later version.                   |
// |                                                                          |
// | This program is distributed in the hope that it will be useful,          |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of           |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            |
// | GNU General Public License for more details.                             |
// |                                                                          |
// | You should have received a copy of the GNU General Public License        |
// | along with this program; if not, write to the Free Software Foundation,  |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.          |
// |                                                                          |
// +--------------------------------------------------------------------------+

    var undefined;

    window.onload = function() {
        var oFCKeditor1 = new FCKeditor( 'message_html' ) ;
        oFCKeditor1.BasePath = geeklogEditorBasePath;
        oFCKeditor1.Config['CustomConfigurationsPath'] = geeklogEditorBaseUrl + '/fckeditor/myconfig.js';
        oFCKeditor1.ToolbarSet = 'editor-toolbar3' ;
        oFCKeditor1.Height = 400 ;
		oFCKeditor1.AutoGrowMax = 1200  ;
        oFCKeditor1.ReplaceTextarea() ;
    };
    function change_editmode(obj) {
        if (obj.value == 'html') {
            document.getElementById('text_editor').style.display='none';
            document.getElementById('html_editor').style.display='';
            swapEditorContent('html');
        } else {
            document.getElementById('text_editor').style.display='';
            document.getElementById('html_editor').style.display='none';
            swapEditorContent('text');
        }
    }

    function getEditorContent() {
        // Get the editor instance that we want to interact with.
        var oEditor = FCKeditorAPI.GetInstance('message_html') ;
        // return the editor contents in XHTML.
        return oEditor.GetXHTML( true );
    }

    function swapEditorContent(curmode) {
        var content = '';
        var oEditor = FCKeditorAPI.GetInstance('message_html') ;
        if (curmode == 'html') { // Switching from Text to HTML mode
            // Get the content from the textarea 'text' content and copy it to the editor
            content = document.getElementById('message_text').value;
            oEditor.SetHTML(content);
        } else {
              content = getEditorContent();
              document.getElementById('message_text').value = content;
          }
    }

    function set_postcontent() {
        if (document.getElementById('sel_editmode').value == 'html') {
            document.getElementById('message_text').value = getEditorContent();
        }
    }