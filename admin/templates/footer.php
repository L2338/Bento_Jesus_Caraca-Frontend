                <!-- Fim do container principal -->
            </div>
            
            <!-- Footer -->
            <footer class="footer mt-auto py-3 bg-light">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <span>&copy; <?php echo date('Y'); ?> Painel Administrativo - Bento de Jesus Caraça</span>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <span>Versão 1.0.0</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Scripts Bootstrap e Gerais -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script do Painel Administrativo -->
    <script src="assets/js/admin-script.js"></script>
    
    <!-- Scripts específicos da página, se houver -->
    <?php if(isset($extra_js)): ?>
    <?php echo $extra_js; ?>
    <?php endif; ?>
</body>
</html> 