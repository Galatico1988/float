import pathlib

class ProjectArchivist:

    def __init__(self, target_extensions: list):
        current_script_dir = pathlib.Path(__file__).parent.resolve()
        
        self.root_dir = current_script_dir.parent
        
        self.extensions = {f".{ext.lstrip('.')}".lower() for ext in target_extensions}
        self.tree_lines = []

    def build_tree(self, directory: pathlib.Path, prefix: str = ""):
        ignored = {'.git', '__pycache__', 'node_modules', '.venv', 'dist'}
        
        entries = [e for e in directory.iterdir() if e.name not in ignored]
        entries.sort(key=lambda x: (not x.is_dir(), x.name.lower()))

        for i, entry in enumerate(entries):
            is_last = i == len(entries) - 1
            connector = "└── " if is_last else "├── "
            self.tree_lines.append(f"{prefix}{connector}{entry.name}")

            if entry.is_dir():
                new_prefix = prefix + ("    " if is_last else "│   ")
                self.build_tree(entry, new_prefix)

    def collect_source_code(self):
        catalog = {ext: [] for ext in self.extensions}
        
        for file in self.root_dir.rglob('*'):
            if file.is_file() and file.suffix.lower() in self.extensions:
                try:
                    catalog[file.suffix.lower()].append({
                        'rel_path': file.relative_to(self.root_dir),
                        'content': file.read_text(encoding='utf-8', errors='replace')
                    })
                except Exception as e:
                    print(f"Erro ao ler {file.name}: {e}")
        return catalog

    def export(self, output_name: str):
        source_data = self.collect_source_code()
        self.build_tree(self.root_dir)

        with open(output_name, 'w', encoding='utf-8') as f:
            f.write(f"--- PROJETO: {self.root_dir.name} ---\n\n")
            
            f.write("## ESTRUTURA DO PROJETO\n")
            f.write("```text\n")
            f.write("\n".join(self.tree_lines))
            f.write("\n```\n\n" + "="*50 + "\n\n")

            for ext, files in source_data.items():
                if not files: continue
                
                f.write(f"### [ SEÇÃO: ARQUIVOS {ext.upper()} ]\n\n")
                for file in files:
                    f.write(f"FILE: {file['rel_path']}\n")
                    f.write(f"{'-' * 40}\n")
                    lang = ext.lstrip('.')
                    f.write(f"```{lang}\n{file['content']}\n```\n\n")
                f.write("\n")

if __name__ == "__main__":
    REQUIRED_EXTS = ['html', 'css', 'js', 'java', 'sql', 'php', 'py']
    
    archivist = ProjectArchivist(REQUIRED_EXTS)
    archivist.export("Projeto_Full_Context.md")
    
    print(f"Processamento concluído na raiz: {archivist.root_dir}")