import os

def transformar_para_png():
    diretorio_atual = os.path.dirname(os.path.abspath(__file__))
    raiz_projeto = os.path.dirname(diretorio_atual)
    
    extensoes_alvo = ['.jpg', '.jpeg', '.bmp', '.webp', '.tiff', '.avif']
    
    print(f"🚀 Iniciando busca na raiz do projeto: {raiz_projeto}")
    
    for raiz, pastas, arquivos in os.walk(raiz_projeto):
        if 'tools' in pastas:
            pastas.remove('tools')
            
        for nome_arquivo in arquivos:
            nome_base, extensao = os.path.splitext(nome_arquivo)
            extensao = extensao.lower()
            
            if extensao in extensoes_alvo and extensao != '.ico':
                caminho_antigo = os.path.join(raiz, nome_arquivo)
                caminho_novo = os.path.join(raiz, nome_base + '.png')
                
                try:
                    os.rename(caminho_antigo, caminho_novo)
                    print(f"✅ Renomeado em {os.path.basename(raiz)}: {nome_arquivo} -> {nome_base}.png")
                except Exception as e:
                    print(f"❌ Erro ao renomear {nome_arquivo}: {e}")

if __name__ == "__main__":
    transformar_para_png()
    print("\n✨ Processo concluído com sucesso!")