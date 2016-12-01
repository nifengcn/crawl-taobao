# desc
抓取淘宝各个分类下的所有店铺数据

# dependency
html解析库:https://github.com/samacs/simple_html_dom

# method

## step 1
抓取淘宝首页(https://www.taobao.com/)的分类,即左侧的女装、男装、内衣。。。

## step 2
抓取各个分类的店铺页面：

https://shopsearch.taobao.com/search?app=shopsearch&spm=a230r.7195193.0.0.3qrKxr&q={$category}&tracelog=shopsearchnoqcat&s={$idx}

{$category}:分类
{$idx}:偏移量，0，20，80。。。,1980(好像都100页)
