import { useState } from "react";
import { Select, MenuItem, Box, FormControl, OutlinedInput } from "@mui/material";

const LanguageSelect = () => {
  const [language, setLanguage] = useState("FR");

  return (
    <FormControl sx={{width: 'auto'}}>
      <Select
        labelId="langue-label"
        id="langue"
        value={language}
        onChange={(e) => setLanguage(e.target.value)}
        displayEmpty
        sx={{
          color: 'white',
          border: '1px solid white',
          '& .MuiSelect-icon': {
            color: 'white',
          },
          '&:hover': {
            borderColor: 'white',
          },
          '& .MuiOutlinedInput-notchedOutline': {
            borderColor: 'white',
          },
        }}
        input={<OutlinedInput label="Langue" />}
      >
        <MenuItem value="FR">
          <Box display="flex" alignItems="center" gap={1}>
            <img src="./assets/france.svg" alt="France" width="20" />
            FR
          </Box>
        </MenuItem>

        <MenuItem value="EN">
          <Box display="flex" alignItems="center" gap={1}>
            <img src="./assets/english.svg" alt="English" width="20" />
            EN
          </Box>
        </MenuItem>

        <MenuItem value="ESP">
          <Box display="flex" alignItems="center" gap={1}>
            <img src="./assets/espagnol.svg" alt="Espagnol" width="20" />
            ESP
          </Box>
        </MenuItem>
      </Select>
    </FormControl>
  );
};

export default LanguageSelect;
