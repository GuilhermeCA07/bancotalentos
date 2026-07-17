# Banco de Talentos

Sistema web para gestão de recrutamento e seleção, desde o cadastro público de candidatos até entrevistas, decisões e contratações. O projeto também oferece dashboard gerencial, controle de acesso por perfil, logs administrativos, autenticação em dois fatores e personalização visual por marca.

> [!IMPORTANT]
> Antes de publicar este repositório, remova e rotacione todas as credenciais presentes nos arquivos de configuração e em arquivos de depuração. Nunca publique senhas de banco, chaves do Cloudflare Turnstile, tokens, segredos SMTP ou dados reais de candidatos.

## Funcionalidades

- Portal público para cadastro e atualização de candidatos.
- Currículo, LinkedIn, categorias, habilidades e dados profissionais.
- Gestão de candidatos, candidaturas e vagas.
- Agendamento, reagendamento e finalização de entrevistas.
- Resultados de entrevista: aprovado, recusado e entrevistado.
- Detalhes da decisão em modais, com data, responsável e observações.
- Chamadas e acompanhamento de disponibilidade por WhatsApp.
- Centro de decisões e fluxo de contratação.
- Encerramento automático de vagas ao atingir o limite de contratações.
- Dashboard com indicadores, alertas, gráficos e filtro por período.
- Departamentos, categorias e habilidades gerenciáveis.
- Temas Oceano, Esmeralda, Violeta, Netcom, SumerNet e NetAki.
- E-mails de token personalizados conforme a identidade visual escolhida.
- Configuração SMTP e teste de envio pelo painel administrativo.
- Logs de inclusão, edição, exclusão, filtros, login, logout e outras ações relevantes.

## Segurança e acesso

O sistema inclui:

- Cloudflare Turnstile nas áreas públicas e de autenticação.
- Autenticação em dois fatores por TOTP, compatível com Google Authenticator.
- Opção de confiar no navegador por 30 dias.
- Troca obrigatória de senha no primeiro acesso para novos usuários.
- Senhas entre 8 e 128 caracteres, com maiúscula, minúscula, número e símbolo.
- Sessão com expiração após uma hora de inatividade.
- Tokens de dispositivos confiáveis armazenados como hash.
- Senhas SMTP e segredos de 2FA protegidos com chaves da aplicação.
- Validação de URLs do LinkedIn com HTTPS e domínio permitido.
- Consultas ao banco realizadas, em sua maioria, com prepared statements.

### Perfis

| Perfil | Acesso principal |
| --- | --- |
| Administrador | Acesso completo, usuários, logs, aparência, SMTP e exclusões |
| Gerente | Operação de recrutamento e gestão de departamentos |
| Recrutador | Candidatos, candidaturas, vagas e processo seletivo |
| Secretário | Entrevistas e chamadas |

Somente administradores podem excluir registros, administrar usuários, consultar logs, alterar a aparência e configurar o e-mail de token.

## Tecnologias

- PHP 8+
- MySQL ou MariaDB
- Apache com `mod_rewrite`
- HTML, CSS e JavaScript
- Chart.js
- PHPMailer
- Google2FA
- BaconQrCode
- Cloudflare Turnstile
- Font Awesome

## Requisitos

- PHP 8.0 ou superior.
- Composer 2.
- MySQL/MariaDB com suporte a JSON.
- Extensões PHP: `mysqli`, `mbstring`, `openssl`, `json`, `ctype`, `filter` e `hash`.
- Apache com `AllowOverride` habilitado para processar o `.htaccess`.
- HTTPS em produção, necessário para cookies seguros e integrações de autenticação.

## Instalação

1. Clone o repositório e acesse a pasta do projeto:

```bash
git clone <url-do-repositorio>
cd <pasta-do-projeto>
```

2. Instale as dependências PHP:

```bash
composer install --no-dev --optimize-autoloader
```

3. Crie um banco MySQL com codificação `utf8mb4` e importe o dump inicial:

```bash
mysql -u SEU_USUARIO -p NOME_DO_BANCO < backup.sql
```

Revise a migration de administradores antes de executá-la em outro ambiente, pois ela promove contas previamente definidas pelo projeto.

4. Configure a conexão em `config/Conexao.php` para o banco do ambiente.

> A implementação atual mantém os parâmetros da conexão nesse arquivo. Para produção ou repositório público, migre esses valores para variáveis de ambiente e mantenha apenas um arquivo de exemplo versionado.

5. Defina chaves exclusivas para proteção dos dados sensíveis:

```text
EMAIL_CONFIG_KEY=<chave-aleatoria-segura>
TWO_FACTOR_KEY=<outra-chave-aleatoria-segura>
```

Uma chave pode ser gerada com:

```bash
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

Não altere essas chaves depois que senhas SMTP ou segredos de 2FA forem gravados, pois os dados existentes deixarão de ser descriptografados.

6. Cadastre chaves próprias do Cloudflare Turnstile em `config/config.php`. Não reutilize chaves de produção em ambientes de desenvolvimento.

7. Garanta permissão de escrita no diretório de currículos:

```text
uploads/curriculos/
```

8. Configure o VirtualHost do Apache com o diretório raiz do projeto como `DocumentRoot` e habilite o `mod_rewrite`.

Após entrar como administrador, configure o servidor SMTP pelo menu **Administração > E-mail do Token** e utilize o teste de envio disponível na própria tela.

## Estrutura do projeto

```text
.
├── config/                 # Conexão, chaves e configuração global
├── controller/             # Controllers e regras de fluxo
├── database/migrations/    # Alterações incrementais do banco
├── helper/                 # Autorização, e-mail, logs e segurança
├── model/                  # Consultas e persistência no MySQL
├── public/
│   ├── css/                # Estilos do portal e painel
│   ├── img/                # Logos, ícones e identidades visuais
│   └── js/                 # Scripts e bibliotecas do frontend
├── tests/                  # Testes auxiliares
├── uploads/curriculos/     # Arquivos enviados pelos candidatos
├── view/                   # Templates organizados por módulo
├── index.php               # Front controller e roteamento
└── composer.json           # Dependências PHP
```

## Roteamento

O projeto utiliza um front controller simples. As rotas são formadas pelos parâmetros `c`, `m` e, quando necessário, `id`:

```text
?c=candidato
?c=entrevista&m=finalizar&id=10
?c=usuario&m=minhaConta
```

- `c`: controller.
- `m`: método público do controller; o padrão é `index`.
- `id`: identificador opcional enviado ao método.

O `.htaccess` direciona requisições que não correspondem a arquivos ou diretórios para o `index.php`.

## Fluxo de recrutamento

```text
Aguardando Entrevista
        ↓
Entrevista Agendada
        ↓
┌─────────────┬─────────────┬──────────────┐
│  Aprovado   │  Recusado   │ Entrevistado │
└─────────────┴─────────────┴──────────────┘
        ↓
Contratado / Dispensado / Auto-Dispensa
```

Uma vaga também pode terminar como **Vaga Fechada** por ação manual ou **Vaga Preenchida por Contratação** ao atingir a quantidade máxima de contratados.

## Verificações

Para validar a sintaxe de todos os arquivos PHP no PowerShell:

```powershell
Get-ChildItem -Recurse -Filter *.php |
    Where-Object { $_.FullName -notmatch '\\vendor\\' } |
    ForEach-Object { php -l $_.FullName }
```

O projeto também contém `tests/dashboard-js-smoke.js`, usado para validar a inicialização dos gráficos do dashboard em um ambiente JavaScript simulado.

## Publicação segura

Antes de enviar o código ao GitHub:

- Remova arquivos de debug e tokens temporários.
- Não versione currículos ou qualquer dado pessoal presente em `uploads/`.
- Remova dumps reais do banco de dados.
- Rotacione senhas e chaves que já tenham sido expostas no histórico.
- Crie um `.gitignore` para segredos, uploads, logs e arquivos locais.
- Use variáveis de ambiente para banco, Turnstile, SMTP e criptografia.
- Confirme que o servidor utiliza HTTPS.
- Revise os usuários promovidos pela migration administrativa.
- Faça backup do banco antes de aplicar migrations em produção.

## Licença

Este repositório ainda não possui um arquivo de licença. Antes de distribuí-lo, adicione uma `LICENSE` compatível com as regras de uso definidas pela organização.
