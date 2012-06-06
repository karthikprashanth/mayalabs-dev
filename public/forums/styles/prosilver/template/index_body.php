<!-- INCLUDE overall_header.html -->
<!-- IF U_MCP --><p align = "right">[&nbsp;<a href="{U_MCP}">{L_MCP}</a>&nbsp;]</p><!-- ENDIF -->

<!-- IF S_DISPLAY_SEARCH or (S_USER_LOGGED_IN and not S_IS_BOT) -->
<ul class="linklist">
	<!-- IF S_DISPLAY_SEARCH -->
		<li><a href="{U_SEARCH_UNANSWERED}">{L_SEARCH_UNANSWERED}</a><!-- IF S_LOAD_UNREADS --> &bull; <a href="{U_SEARCH_UNREAD}">{L_SEARCH_UNREAD}</a><!-- ENDIF --><!-- IF S_USER_LOGGED_IN --> &bull;<a href="{U_SEARCH_NEW}">{L_SEARCH_NEW}</a><!-- ENDIF --> &bull; <a href="{U_SEARCH_ACTIVE_TOPICS}">{L_SEARCH_ACTIVE_TOPICS}</a></li>
	<!-- ENDIF -->
	<!-- IF not S_IS_BOT and U_MARK_FORUMS --><li class="rightside"><a href="{U_MARK_FORUMS}" accesskey="m">{L_MARK_FORUMS_READ}</a></li><!-- ENDIF -->
</ul>
<!-- ENDIF -->
<!-- INCLUDE forumlist_body.html -->
<!-- INCLUDE overall_footer.html -->