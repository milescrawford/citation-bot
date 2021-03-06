<?php
/*
 * Contains constants and text-parsing functions for wikitext comments.
 */

// If you ever change PLACEHOLDER_TEXT, please update expandFns.php::remove_comments

abstract class WikiThings {
  const TREAT_IDENTICAL_SEPARATELY = FALSE;
  protected $rawtext;

  public function parse_text($text) {
    $this->rawtext = $text;
  }

  public function parsed_text() {
    return $this->rawtext;
  }
}

final class Comment extends WikiThings {
  const PLACEHOLDER_TEXT = '# # # CITATION_BOT_PLACEHOLDER_COMMENT %s # # #';
  const REGEXP = '~<!--.*?-->~us';
}

final class Nowiki extends WikiThings {
  const PLACEHOLDER_TEXT = '# # # CITATION_BOT_PLACEHOLDER_NOWIKI %s # # #';
  const REGEXP = '~<nowiki>.*?</nowiki>~us';
}

final class Chemistry extends WikiThings {
  const PLACEHOLDER_TEXT = '# # # CITATION_BOT_PLACEHOLDER_CHEMISTRY %s # # #';
  const REGEXP = '~<chem>.*?</chem>~us';
}

final class Mathematics extends WikiThings {
  const PLACEHOLDER_TEXT = '# # # CITATION_BOT_PLACEHOLDER_MATHEMATICS %s # # #';
  const REGEXP = '~<math>.*?</math>~us';
}

final class Musicscores extends WikiThings {
  const PLACEHOLDER_TEXT = '# # # CITATION_BOT_PLACEHOLDER_MUSIC %s # # #';
  const REGEXP = '~<score>.*?</score>~us';
}

final class Preformated extends WikiThings {
  const PLACEHOLDER_TEXT = '# # # CITATION_BOT_PLACEHOLDER_PREFORMAT %s # # #';
  const REGEXP = '~<pre>.*?</pre>~us';
}

final class SingleBracket extends WikiThings {
  const PLACEHOLDER_TEXT = '# # # CITATION_BOT_PLACEHOLDER_SINGLE_BRACKET %s # # #';
  const REGEXP = '~(?<!\{)\{[^\{\}]+\}(?!\})~us';
}
