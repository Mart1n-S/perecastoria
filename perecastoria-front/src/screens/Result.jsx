import React from 'react';
import { Box, Typography } from '@mui/material';

const Result = ({ storyData, audioUrl, imageBase64 }) => {
  const base64Audio = audioUrl.startsWith("data:") ? audioUrl : `data:audio/mp3;base64,${audioUrl}`;

  return (
    <Box
      sx={{
        width: '80vw',
        p: 4,
        borderRadius: 3,
        boxShadow: 3,
        textAlign: 'center',
      }}
    >
      <Box sx={{display: 'flex', alignItems: 'center', gap: '20px'}}>
        <Typography variant="h5" color="white" paragraph>
          {storyData}
        </Typography>

        <Box sx={{ mb: 3 }}>
          <img
            src={`data:image/jpeg;base64,${imageBase64}`}
            alt="Generated"
            style={{ width: '400px', borderRadius: 8 }}
          />
        </Box>
      </Box>

      {audioUrl && (
        <Box>
          <audio controls>
            <source src={base64Audio} type="audio/mp3" />
            Your browser does not support the audio element.
          </audio>
        </Box>
      )}
    </Box>
  );
};

export default Result;
