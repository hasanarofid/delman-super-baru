import os
import re

directories_to_scan = [
    'resources/views',
    'app/Http/Controllers'
]

# Replacements:
# Pendampingan -> Pengawasan
# pendampingan -> pengawasan
# Pendamping -> Pengawas
# pendamping -> pengawas

# Safe replacement regex functions
def replace_text(content):
    # Pendampingan (capitalized) -> Pengawasan
    # Lookaround: don't match if preceded by a letter, $, /, _, ., or - or inside route('...')
    # Also ignore if followed by a letter, _, or .
    
    # Pendampingan
    content = re.sub(r'(?<![a-zA-Z$/_.-])Pendampingan(?![a-zA-Z_])', 'Pengawasan', content)
    
    # pendampingan
    # Avoid `$pendampingan`, `route('pendampingan`, `href="/pendampingan`, `tgl_pendampingan`
    content = re.sub(r'(?<![a-zA-Z$/_.-])(?<!route\(\')(?<!route\(")pendampingan(?![a-zA-Z_])', 'pengawasan', content)
    
    # Pendamping
    content = re.sub(r'(?<![a-zA-Z$/_.-])Pendamping(?![a-zA-Z_])', 'Pengawas', content)
    
    # pendamping
    content = re.sub(r'(?<![a-zA-Z$/_.-])(?<!route\(\')(?<!route\(")pendamping(?![a-zA-Z_])', 'pengawas', content)
    
    return content

changed_files = []

for directory in directories_to_scan:
    for root, dirs, files in os.walk(directory):
        for file in files:
            if file.endswith('.php'):
                filepath = os.path.join(root, file)
                
                try:
                    with open(filepath, 'r', encoding='utf-8') as f:
                        original_content = f.read()
                        
                    new_content = replace_text(original_content)
                    
                    if original_content != new_content:
                        with open(filepath, 'w', encoding='utf-8') as f:
                            f.write(new_content)
                        changed_files.append(filepath)
                except Exception as e:
                    print(f"Error processing {filepath}: {e}")

print(f"Total files updated: {len(changed_files)}")
for cf in changed_files:
    print(f"- {cf}")
