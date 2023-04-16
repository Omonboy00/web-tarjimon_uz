from django.db import models

class Lugat(models.Model):

    ruscha = models.CharField('Ruscha', max_length=128)
    inglizcha = models.CharField('Inglizcha', max_length=128)
    uzbekcha = models.CharField('O`zbekcha', max_length=128)
    
