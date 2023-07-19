<?php
    // Grab SQL File
    $directory = __DIR__;
    $file = $directory . '/sweetwater_test.sql';
    $sweetwaterContents = file_get_contents($file);

    // Regular expression pattern to match comments
    $pattern = "/\(\d+,'((?:\\\\'|[^'])*)'/s";
    preg_match_all($pattern, $sweetwaterContents, $matches);

    $comments = $matches[1];
    $comments = array_map(function ($comment) {
        return str_replace("\\'", "'", $comment);
    }, $comments);

    $categories = array(
        'Candy' => array(),
        'Call Me / Don\'t Call Me' => array(),
        'Referred' => array(),
        'Signature Requirements' => array(),
        'Miscellaneous' => array(),
    );

    $position = 0;

    foreach ($matches[1] as $comment) {
        // Decode special characters like  (PU2)
        $comment = mb_convert_encoding($comment, 'ISO-8859-1', 'UTF-8');
        
        // Remove newlines and extra spaces from the comment
        $comment = str_replace(array("\n", "\r"), ' ', $comment);
        $comment = preg_replace('/\s+/', ' ', $comment);
        
        // echo "Extracted comment: $comment\n";
        $comment = stripslashes($comment);

        if (stripos($comment, 'candy') !== false) {
            $categories['Candy'][] = $comment;
        } elseif (stripos($comment, 'call me') !== false || stripos($comment, "don't call me") !== false) {
            $categories['Call Me / Don\'t Call Me'][] = $comment;
        } elseif (stripos($comment, 'referred') !== false) {
            $categories['Referred'][] = $comment;
        } elseif (stripos($comment, 'signature') !== false) {
            $categories['Signature Requirements'][] = $comment;
        } else {
            $categories['Miscellaneous'][] = $comment;
        }
    }

    // Display the categorized comments
    foreach ($categories as $category => $comments) {
        echo "Comments about $category:\n";
        foreach ($comments as $comment) {
            echo "<pre>- $comment</pre>\n";
        }
        echo "\n";
    }
?>