import json
import base64
import tempfile
import os
import sys
import io
import subprocess
import platform
from docxtpl import DocxTemplate

def get_soffice_path():
    """
    Devuelve el path al ejecutable de LibreOffice (soffice).
    - En Linux/Mac asume que está en el PATH.
    - En Windows busca en Program Files.
    """
    system_os = platform.system().lower()

    if "windows" in system_os:
        posibles_rutas = [
            r"C:\Program Files\LibreOffice\program\soffice.exe",
            r"C:\Program Files (x86)\LibreOffice\program\soffice.exe"
        ]
        for ruta in posibles_rutas:
            if os.path.exists(ruta):
                return ruta
        raise FileNotFoundError(
            "No se encontró LibreOffice (soffice.exe) en Windows. "
            "Asegúrate de instalarlo o agregarlo al PATH."
        )
    else:
        # Linux/Mac → se asume que está en el PATH
        return "soffice"

def main():
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding="utf-8")
    raw = sys.stdin.read()
    data = json.loads(raw)

    plantilla = data["plantilla"]
    variables = data["variables"]

    # Cargar plantilla con docxtpl
    doc = DocxTemplate(plantilla)

    # Insertar tabla entregables si está en los datos
    if "table_entregables" in variables:
        table_data = variables["table_entregables"]

        table = []
        for bloque in table_data:
            titulo = bloque.get("titulo", "")
            items = "\n".join(["* " + it for it in bloque.get("items", [])])
            precio = bloque.get("precio", "")
            table.append({"titulo": titulo, "items": items, "precio": precio})

        variables["table_entregables"] = table

    # Renderizar variables en la plantilla
    doc.render(variables)

    # Guardar Word temporal
    temp_docx = tempfile.mktemp(suffix=".docx")
    doc.save(temp_docx)

    # Definir carpeta de salida (usar la misma carpeta del docx temporal)
    outdir = os.path.dirname(temp_docx)

    # Ejecutable de LibreOffice
    soffice_cmd = get_soffice_path()

    try:
        # Ejecutar conversión, capturando stdout y stderr para depuración
        result = subprocess.run([
            soffice_cmd,
            "--headless",
            "--norestore",
            "--convert-to",
            "pdf",
            temp_docx,
            "--outdir",
            outdir
        ], capture_output=True, text=True, check=True)

        # Ajustar la ruta real generada por LibreOffice
        generated_pdf = os.path.splitext(temp_docx)[0] + ".pdf"
        if not os.path.exists(generated_pdf):
            raise FileNotFoundError("LibreOffice no generó el PDF.")

        # Devolver el PDF en base64
        with open(generated_pdf, "rb") as f:
            pdf_b64 = base64.b64encode(f.read()).decode("utf-8")

        print(pdf_b64, end="")

    except subprocess.CalledProcessError as e:
        # Mostrar error con salida estándar y error para facilitar diagnóstico
        print(f"Error al convertir a PDF: {e}\nSTDOUT:\n{e.stdout}\nSTDERR:\n{e.stderr}", file=sys.stderr)
        sys.exit(1)
    except Exception as e:
        print(f"Error inesperado: {e}", file=sys.stderr)
        sys.exit(1)
    finally:
        # Limpieza de archivos temporales
        if os.path.exists(temp_docx):
            os.remove(temp_docx)
        if 'generated_pdf' in locals() and os.path.exists(generated_pdf):
            os.remove(generated_pdf)

if __name__ == "__main__":
    main()
