# Orientação Técnica — Atualização PHP 8.3.36 e Erro "No input file specified"

> **Data:** 22/10/2026  
> **Responsável:** Sistema de Manutenção  
> **Projeto afetado:** `procseletivos` (CodeIgniter 4)  
> **PHP:** 8.3.16 → 8.3.36 (NTS, Win32, VS16, x64)

---

## 1. Resumo do Problema

Após atualizar o PHP para a versão **8.3.36** (corrigindo as vulnerabilidades CVE-2026-109618 e CVE-2026-109619), algumas telas que antes funcionavam passaram a exibir o erro:

```
No input file specified.
```

Telas afetadas no projeto:
- `http://localhost/servicos/procseletivos/Cadastros`
- `http://localhost/servicos/procseletivos/adm/login` (após POST de autenticação)
- Qualquer outra rota que dependa de URL rewriting (rotas amigáveis).

Telas que **continuaram funcionando** (porque não usam PATH_INFO):
- Tela inicial pública (`/`)
- Tela de login do admin (`/adm/`)

---

## 2. Causa Raiz

O PHP 8.3.36 incluiu correções de segurança que **restringem o comportamento do `cgi.fix_pathinfo`** — justamente o mecanismo que as CVEs exploravam para permitir execução remota de código (RCE).

### O que é PATH_INFO?

No Apache com `mod_rewrite`, quando usamos regras como:

```apache
RewriteRule ^(.*)$ index.php/$1 [L]
```

O servidor repassa a URL para o PHP assim:

```
index.php/Cadastros
     ^^^^^^^^^^^^
     PATH_INFO
```

Isso depende do PHP interpretar o que vem **após** o arquivo `.php` como informação de caminho (PATH_INFO). Com as correções de segurança da versão 8.3.36, o PHP-CGI passou a rejeitar esse formato em determinadas condições, resultando em **"No input file specified"**.

> ⚠️ **NÃO reative `cgi.fix_pathinfo=1`** no `php.ini`. Isso reabriria a vulnerabilidade de segurança que você acabou de corrigir atualizando o PHP.

---

## 3. Solução Oficial e Segura

A maneira correta de resolver o problema **sem desfazer a correção de segurança** é alterar a regra de rewrite do Apache para passar a URI como **query string** (`?`) em vez de **PATH_INFO** (`/`).

### Antes (problemático no PHP 8.3.36+)

```apache
RewriteRule ^(.*)$ index.php/$1 [L]
```

### Depois (compatível e seguro)

```apache
RewriteRule ^(.*)$ index.php?/$1 [L,QSA]
```

A única diferença é a substituição de:
- `/` por `?`
- Adição da flag `QSA` (Query String Append) para preservar parâmetros GET existentes

O CodeIgniter 4 detecta a URI corretamente tanto via PATH_INFO quanto via query string, então **nenhuma alteração no código-fonte PHP é necessária**.

---

## 4. Arquivos Alterados neste Projeto

| Arquivo | Alteração |
|---------|-----------|
| `procseletivos/.htaccess` | `RewriteRule ^(.*)$ public/index.php?/$1 [L,QSA]` |
| `procseletivos/adm/.htaccess` | `RewriteRule ^([\s\S]*)$ index.php?/$1 [L,NC,QSA]` |

---

## 5. Como Aplicar em Outros Projetos CodeIgniter 4

### Passo 1: Localize todos os `.htaccess` com rewrite

Procure por arquivos `.htaccess` que contenham regras do tipo:

```apache
RewriteRule ... index.php/ ...
```

### Passo 2: Altere de `/` para `?`

Mude:
- `index.php/$1` → `index.php?/$1`
- Adicione a flag `QSA` se ainda não estiver presente

### Passo 3: Reinicie o Apache

Após alterar os `.htaccess`, reinicie o Apache para garantir que as mudanças sejam aplicadas:

- Pelo painel do Laragon: **Menu > Apache > Restart**
- Ou pelo serviço do Apache diretamente

### Passo 4: Teste as rotas principais

Acesse:
- A página inicial
- Uma rota interna (ex: `/Cadastros`, `/adm/login`)
- Uma rota com parâmetros GET (ex: `?foo=bar`) para confirmar que `QSA` está funcionando

---

## 6. Verificação Rápida

Para confirmar que o problema é o mesmo em outro projeto, verifique se o `.htaccess` contém esta linha (com barra `/` após `index.php`):

```bash
grep -n "index.php/" .htaccess
```

Se houver correspondência, aplique a correção descrita acima.

---

## 7. Considerações Finais

- ✅ A correção **não afeta a segurança** — pelo contrário, mantém as proteções da versão 8.3.36.
- ✅ **Não é necessário alterar o `php.ini`** nem reabilitar `cgi.fix_pathinfo`.
- ✅ **Não é necessário alterar código PHP** — o CodeIgniter 4 já suporta ambos os modos de passagem de URI.
- ✅ Funciona tanto em servidores Windows (Laragon/XAMPP) quanto Linux.

---

## 8. Referências

- CodeIgniter 4 User Guide — [URL Routing](https://codeigniter.com/user_guide/incoming/routing.html)
- PHP Manual — [cgi.fix_pathinfo](https://www.php.net/manual/en/ini.core.php#ini.cgi.fix-pathinfo)
- CVE-2026-109618 / CVE-2026-109619 — Correções de segurança do PHP 8.3.36
