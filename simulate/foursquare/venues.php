<?php
$data = file_get_contents('https://api.foursquare.com/v2/venues/categories?oauth_token=PIGX1HYP0G2AH5KYGQ4KPSZP33AZKC0SKPE4DL3FUN4B3YWD&v=20130515');

function getCategory($cat) {
    $output = '';
    $output .= '<li class="category' . (isset($cat->categories) && count($cat->categories) > 0? ' has-subcategories' : '') . '">';
    $output .= '<img src="'. $cat->icon->prefix . 'bg_88' . $cat->icon->suffix . '">';
    $output .= '<span>' . $cat->name . '</span>';
    
    if(isset($cat->categories) && count($cat->categories) > 0) {
        $output .= '<ul>';
        foreach($cat->categories as $category) {
            $output .= getCategory($category);
        }
        $output .= '</ul>';
    }

    $output .= '</li>';
    
    return $output;
}

$response = json_decode($data)->response;

$output = [];
?>

<ul>
<?php foreach($response->categories as $category): ?>
    <?= getCategory($category); ?>
<?php endforeach; ?>
</ul>

<style>
.category {
    width: 160px;
    float: left;
    background: #eee;
    border: 2px solid #ddd;
    padding: 8px;
    margin: 10px;
}
.category img,
.category span {
    display: block;
    margin: 0 auto;
    text-align: center;
}

.category.has-subcategories {
    clear: left;
    float: none;
    width: auto;
    overflow: auto;
}

.category.has-subcategories > span {
    border-bottom: 2px solid #ddd;
}
</style>
