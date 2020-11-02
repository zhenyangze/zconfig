wordpress config
----

wordpress通用配置插件，可制作网站设置，自定义配置，多列配置等。

### 后台截图

![demo]()



### API示例

```shell
/?plugin=zconfig_api&action=config&code=banner_list&sub_key=single&sub_value=男
```

```javascript
{
	"code": 200,
	"msg": "获取成功",
	"data": {
		"id": "1",
		"name": "轮播图",
		"code": "banner_list",
		"comment": "设置所有页面的轮播图效果，统一按一种格式处理",
		"data": {
			"image": "",
			"title": "轮播图",
			"link": "链接",
			"lins": "多行文本多行文本",
			"single": "男",
			"multi": [
				"生物"
			]
		}
	}
}
```



