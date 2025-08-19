# 📄 Dados Extras para Cadastro PJ para WooCommerce

![Banner do Plugin DECPJ](https://i.imgur.com/u1D0L4n.png)

### Um plugin completo para adicionar, validar e gerenciar campos de Pessoa Jurídica no WooCommerce.

> Este plugin foi desenvolvido para suprir uma necessidade crucial em lojas virtuais brasileiras que operam no modelo B2B: a coleta e validação de dados de clientes Pessoa Jurídica (PJ) de forma nativa, inteligente e integrada ao WooCommerce. Criado por **Yago Prata (MXR Studio)**, o DECPJ transforma o processo de cadastro e checkout para empresas.

<p align="center">
  <img src="https://img.shields.io/badge/versão-1.4.0-blue?style=for-the-badge" alt="Versão do Plugin">
  <img src="https://img.shields.io/github/last-commit/oyagoprata/woocommerce-pj-fields?style=for-the-badge" alt="Último Commit">
  <img src="https://img.shields.io/badge/licença-GPLv2_ou_posterior-green?style=for-the-badge" alt="Licença">
  <img src="https://img.shields.io/badge/WordPress-6.5+-blueviolet?style=for-the-badge" alt="Versão WordPress">
  <img src="https://img.shields.io/badge/WooCommerce-8.8+-purple?style=for-the-badge" alt="Versão WooCommerce">
</p>

---

## ✨ Funcionalidades Principais

O BFPJ vai muito além de simplesmente adicionar campos. É uma ferramenta completa de gestão de dados.

* ✅ **Gerenciador de Campos Dinâmico:** Crie, edite, remova e reordene campos com total liberdade através de uma interface moderna de arrastar e soltar.
* ✅ **Validação de CNPJ:** Garanta a integridade dos dados com uma validação matemática que impede o cadastro de CNPJs inválidos.
* ✅ **Integração Total com WooCommerce:** Os dados aparecem no checkout, na página "Minha Conta", nos e-mails transacionais e, mais importante, no painel de detalhes do pedido para o administrador.
* ✅ **Compatível com Exportação de Clientes:** Os campos personalizados são adicionados como novas colunas na ferramenta de exportação de clientes nativa do WooCommerce.
* ✅ **Atualizações via GitHub:** O plugin notifica sobre novas versões e permite a atualização com um clique, diretamente do painel do WordPress, lendo as releases deste repositório.
* ✅ **Painel de Controle Intuitivo:** Uma interface de administração moderna, com campos recolhíveis (accordion) e opções claras para uma experiência de uso agradável.
* ✅ **Alta Performance:** Compatível com o recurso de **HPOS** (High-Performance Order Storage) do WooCommerce, garantindo escalabilidade para sua loja.
* ✅ **Máscaras de Input:** Aplica máscaras de formatação automática para campos como CNPJ e Telefone, melhorando a experiência de preenchimento do usuário.

---

## 📸 Telas do Plugin

<p align="center">
  <strong>Painel de Configurações Moderno e Intuitivo</strong><br>
  <em>Gerencie todos os seus campos em um só lugar com uma interface de arrastar e soltar.</em><br>
  <img src="https://i.imgur.com/YwN3i8a.png" alt="Painel de Configurações do Plugin" width="700">
</p>

<p align="center">
  <strong>Campos no Checkout</strong><br>
  <em>Os campos são perfeitamente integrados ao formulário de finalização de compra.</em><br>
  <img src="https://i.imgur.com/o1bXzJq.png" alt="Campos no formulário de checkout" width="700">
</p>

<p align="center">
  <strong>Dados no Detalhe do Pedido (Admin)</strong><br>
  <em>Consulte os dados de PJ diretamente na página do pedido para agilizar o faturamento.</em><br>
  <img src="https://i.imgur.com/177z5dF.png" alt="Dados de PJ no painel de pedidos do admin" width="700">
</p>

---

## 🔧 Instalação

Existem duas maneiras de instalar o plugin:

#### **Método 1: Via Painel WordPress (Recomendado)**

1.  Neste repositório, vá para a seção **[Releases](https://github.com/oyagoprata/woocommerce-pj-fields/releases)**.
2.  Baixe o arquivo `.zip` da versão mais recente.
3.  No seu painel WordPress, vá para `Plugins > Adicionar Novo > Enviar plugin`.
4.  Escolha o arquivo `.zip` que você baixou e clique em **Instalar agora**.
5.  Ative o plugin.

#### **Método 2: Via Git (Para Desenvolvedores)**

Navegue até o diretório de plugins do seu site e clone o repositório:
```bash
cd wp-content/plugins/
git clone [https://github.com/oyagoprata/woocommerce-pj-fields.git](https://github.com/oyagoprata/woocommerce-pj-fields.git)
```
Depois, ative o plugin no painel do WordPress.

---

## ⚙️ Uso e Configuração

Após a ativação, um novo menu aparecerá em **WooCommerce > Campos de Cadastro PJ**.

Nesta página, você pode:
* **Adicionar um Novo Campo:** Clicando no botão e preenchendo as informações.
* **Reordenar os Campos:** Clicando e arrastando-os pela alça de movimento.
* **Configurar um Campo:** Expandindo o card para editar seu rótulo, placeholder, obrigatoriedade, posição e regras de validação.
* **Remover um Campo:** Clicando no link "Remover" em qualquer campo que você tenha criado.

---

## 🚀 Futuras Melhorias (Roadmap)

Este projeto está em desenvolvimento ativo. As próximas funcionalidades planejadas incluem:

* 💡 **Consulta de CNPJ em Tempo Real:** Preenchimento automático de dados da empresa a partir da API da ReceitaWS.
* 💡 **Máscaras de Campo Dinâmicas:** Escolha de máscaras (CEP, IE, etc.) para qualquer campo.
* 💡 **Integração com Faturas em PDF:** Inclusão automática dos dados de PJ em faturas de plugins populares.
* 💡 **Mais Tipos de Campo:** Suporte para `select`, `textarea`, `radio`, etc.

---

## 📄 Licença

Este plugin é distribuído sob a licença **GPLv2 ou posterior**.

---

## 👨‍💻 Autor

Desenvolvido com ❤️ por **Yago Prata** da **MXR Studio**.

[![MXR Studio](https://img.shields.io/badge/MXR%20Studio-Visite%20nosso%20site-black?style=flat&logo=data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTIyIDEwSDEyVjJMMjIgMTBaTTExIDEzSDFWDIxTDExIDEzWiIgZmlsbD0id2hpdGUiLz4KPC9zdmc+Cg==)](https://mxrstudio.com.br)
