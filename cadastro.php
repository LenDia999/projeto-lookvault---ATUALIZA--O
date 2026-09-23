<?php
$pageTitle = "LOOKVAULT · Cadastrar Peça";
$pageDescription = "Adicione novas peças ao seu guarda-roupa.";
include 'includes/header.php';
?>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Cadastrar Nova Peça</h1>
            <p class="page-subtitle">Adicione uma nova peça ao seu guarda-roupa digital</p>
        </div>
    </section>

    <section class="form-section">
        <div class="container">
            <div class="form-wrapper">
                <form action="processar-cadastro.php" method="POST" enctype="multipart/form-data" class="cadastro-form">
                    <div class="form-group">
                        <label for="nome">Nome da Peça</label>
                        <input type="text" id="nome" name="nome" placeholder="Ex: Camisa Branca" required />
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="marca">Marca</label>
                            <select id="marca" name="marca" required>
                                <option value="">Selecione a marca</option>
                                <option value="DIOR">DIOR</option>
                                <option value="GUCCI">GUCCI</option>
                                <option value="CHANEL">CHANEL</option>
                                <option value="PRADA">PRADA</option>
                                <option value="FENDI">FENDI</option>
                                <option value="Outra">Outra</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="categoria">Categoria</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Selecione a categoria</option>
                                <option value="Camisas">Camisas</option>
                                <option value="Calças">Calças</option>
                                <option value="Sapatos">Sapatos</option>
                                <option value="Acessórios">Acessórios</option>
                                <option value="Blazers">Blazers</option>
                                <option value="Vestidos">Vestidos</option>
                                <option value="Jeans">Jeans</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cor">Cor</label>
                            <input type="text" id="cor" name="cor" placeholder="Ex: Branco, Azul, Preto" required />
                        </div>

                        <div class="form-group">
                            <label for="valor">Valor (R$)</label>
                            <input type="number" id="valor" name="valor" placeholder="0,00" step="0.01" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="tags">Tags (separadas por vírgula)</label>
                        <input type="text" id="tags" name="tags" placeholder="Ex: verão, casual, social" />
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto da Peça</label>
                        <div class="file-upload">
                            <input type="file" id="foto" name="foto" accept="image/*" />
                            <label for="foto" class="file-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Clique para selecionar uma imagem</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i> Salvar Peça
                        </button>
                        <a href="guarda-roupa.php" class="btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>