# 🚀 虚拟主机部署快速指南（PHP 7.4）

## 📋 部署前准备

### 1. 本地测试

确保在本地测试通过：

```bash
# Windows 用户
check-deployment.bat

# Linux/Mac 用户
chmod +x check-deployment.sh
./check-deployment.sh
```

### 2. 生成密钥

```bash
php generate-secret.php
```

将生成的密钥复制到 `config.php` 中。

### 3. 配置 GitHub OAuth

访问 https://github.com/settings/developers 创建 OAuth App：

- **Homepage URL**: https://yourdomain.com
- **Callback URL**: https://yourdomain.com/admin/auth.php?action=callback

获取 Client ID 和 Client Secret 后，更新 `config.php`。

---

## 📤 上传文件到虚拟主机

### 使用 FTP 上传（FileZilla）

1. **连接设置**
   - 主机: yourdomain.com
   - 用户名: FTP 用户名
   - 密码: FTP 密码
   - 端口: 21

2. **上传文件**
   - 远程目录: `/public_html` 或 `/www`
   - 上传以下文件和目录：
     ```
     ✅ admin/
     ✅ api/
     ✅ content/
     ✅ includes/
     ✅ public/
     ✅ templates/
     ✅ vendor/           # Composer 依赖
     ✅ index.php
     ✅ post.php
     ✅ archive.php
     ✅ search.php
     ✅ page.php
     ✅ rss.php
     ✅ config.php
     ✅ .htaccess
     ```

### 使用 cPanel 文件管理器

1. 登录 cPanel
2. 文件管理器 → public_html
3. 上传 → 选择文件
4. 上传后解压（如果上传了 zip）

---

## ⚙️ 配置虚拟主机

### cPanel 用户

1. **设置 PHP 版本**
   - Software → MultiPHP Manager
   - 选择域名为 PHP 7.4

2. **安装 SSL 证书**
   - Security → SSL/TLS Status
   - 为域名安装 Let's Encrypt

3. **检查文件权限**（通常自动）
   - 右键文件 → Change Permissions
   - 目录: 755
   - 文件: 644

### 宝塔面板用户

1. **创建站点**
   - 网站 → 添加站点
   - 填写域名
   - 选择 PHP 7.4
   - 创建

2. **上传文件**
   - 文件 → 网站根目录
   - 上传文件

3. **设置伪静态**（已包含在 .htaccess）
   - 网站 → 设置 → 伪静态
   - 选择当前或保持默认

---

## 🧪 测试部署

### 1. 基础功能测试

访问以下 URL：

```
✅ https://yourdomain.com/
   → 应显示首页

✅ https://yourdomain.com/archive
   → 应显示归档页

✅ https://yourdomain.com/admin/auth.php
   → 应显示登录页面

✅ https://yourdomain.com/rss
   → 应显示 RSS XML
```

### 2. 后台登录测试

1. 访问 `/admin/auth.php`
2. 点击"使用 GitHub 登录"
3. 授权后应跳转到后台首页
4. 能正常访问说明配置正确

### 3. 创建文章测试

1. 点击"新建文章"
2. 输入标题和内容
3. 点击"保存"
4. 访问前台查看文章

---

## ⚠️ 常见问题排查

### 问题 1: 404 Not Found

**原因**: URL 重写未生效

**解决**:
- 确认 `.htaccess` 已上传
- cPanel: Software → MultiPHP Manager → 启用 mod_rewrite
- 宝塔: 网站 → 设置 → 伪静态 → 选择对应规则
- 联系主机商确认是否支持 mod_rewrite

### 问题 2: 500 Internal Server Error

**原因**: PHP 语法错误或权限问题

**解决**:
1. 查看错误日志：
   - cPanel: Logs → Error Log
   - 宝塔: 网站 → 日志
2. 检查 PHP 版本是否为 7.4+
3. 检查目录权限：
   ```bash
   chmod 755 content
   chmod 755 public/uploads
   ```
4. 临时开启错误显示（config.php）：
   ```php
   define('DEBUG_MODE', true);
   ```

### 问题 3: 无法登录后台

**原因**: GitHub OAuth 配置错误

**解决**:
1. 确认 GitHub OAuth App 配置正确
2. 确认 Callback URL 完全匹配（包括 https 和结尾没有斜杠）
3. 确认用户名在白名单中
4. 检查 config.php 中的配置

### 问题 4: 图片无法上传

**原因**: 目录权限不足

**解决**:
```bash
chmod 755 public/uploads
chmod 755 public/uploads/2024
chmod 755 public/uploads/2024/01
# ... 其他年份目录
```

### 问题 5: 样式显示异常

**原因**: CDN 无法访问或路径错误

**解决**:
1. 检查网络能否访问 CDN（unpkg.com, cdn.tailwindcss.com）
2. 确认 public/assets/css/ 文件存在
3. 浏览器 Ctrl+F5 强制刷新
4. 检查浏览器控制台错误

### 问题 6: 音乐无法播放

**原因**: API 不可用或跨域问题

**解决**:
1. 确认 NETEASE_API 可访问
2. 检查浏览器控制台错误
3. 暂时禁用音乐功能（从模板删除相关代码）

---

## 🔧 优化建议

### 1. 启用 OPcache

在 `.htaccess` 添加：

```apache
<IfModule mod_php.c>
    php_value opcache.enable 1
    php_value opcache.memory_consumption 128
    php_value opcache.max_accelerated_files 10000
    php_value opcache.revalidate_freq 60
</IfModule>
```

### 2. 配置 CDN

推荐使用：
- **国内**: 阿里云 CDN、腾讯云 CDN
- **国外**: Cloudflare

### 3. 启用 GZIP

已包含在 `.htaccess` 中，确保 mod_deflate 启用。

---

## 📊 性能检查

### 使用 GTmetrix

1. 访问 https://gtmetrix.com
2. 输入网站 URL
3. 查看性能报告
4. 根据建议优化

### 使用 Google PageSpeed

1. 访问 https://pagespeed.web.dev
2. 分析网站
3. 查看优化建议

---

## 🎉 部署完成检查清单

- [ ] 首页正常访问
- [ ] 所有页面链接正常
- [ ] 后台可以登录
- [ ] 可以创建文章
- [ ] 主题切换正常
- [ ] 搜索功能正常
- [ ] 移动端显示正常
- [ ] SSL 证书已安装
- [ ] RSS 订阅正常
- [ ] 图片上传正常

---

## 📞 需要帮助？

### 文档
- 完整文档: `README-PHP.md`
- 部署指南: `DEPLOY-PHP74.md`
- 上传指南: `UPLOAD-GUIDE.md`

### 虚拟主机商
- 联系主机商技术支持
- 查看主机商帮助文档

### 在线资源
- PHP 官方文档: https://www.php.net/docs.php
- Apache .htaccess: https://httpd.apache.org/docs/current/howto/htaccess.html
- cPanel 文档: https://docs.cpanel.net/

---

**祝你部署成功！** 🎊

如果遇到问题，请检查：
1. 错误日志
2. PHP 版本
3. 文件权限
4. 配置文件
